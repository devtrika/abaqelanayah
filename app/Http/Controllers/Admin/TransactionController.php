<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     */
    public function index()
    {
if (request()->ajax()) {
    // Extract searchArray and remove 'user_name' before passing to the search() scope
    $searchArray = request()->searchArray ?? [];
    $userNameTerm = null;
    if (isset($searchArray['user_name'])) {
        $userNameTerm = trim($searchArray['user_name']);
        unset($searchArray['user_name']);
    }

    $transactionsQuery = Transaction::with(['user', 'order'])
        ->search($searchArray)
        ->latest();

    // Apply relational filtering for the user name (or numeric id)
    if (!empty($userNameTerm)) {
        $term = $userNameTerm;

        // If the input is numeric, treat it as a direct user_id lookup
        if (is_numeric($term)) {
            $transactionsQuery->where('user_id', $term);
        } else {
            // Split into words and search each word in name OR email (AND across words)
            $words = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($words)) {
                $transactionsQuery->whereHas('user', function ($q) use ($words) {
                    foreach ($words as $word) {
                        $w = "%{$word}%";
                        $q->where(function ($q2) use ($w) {
                            $q2->where('name', 'LIKE', $w)
                               ->orWhere('email', 'LIKE', $w);
                        });
                    }
                });
            }
        }
    }

    $transactions = $transactionsQuery->paginate(30);

    $html = view('admin.transactions.table', compact('transactions'))->render();
    return response()->json(['html' => $html]);
}
        

        return view('admin.transactions.index');
    }

    /**
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'order'])->findOrFail($id);
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Accept a withdraw/transaction request
     */
    public function accept(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        DB::beginTransaction();
        try {
            // If this is a wallet withdraw, deduct from user's wallet balance on acceptance
            if ($transaction->type === 'wallet-withdraw') {
                $user = $transaction->user;
                if (! $user) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => __('admin.provider_not_found') ?? 'User not found'], 404);
                }

                $amount = abs($transaction->amount);
                if ($user->wallet_balance < $amount) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => __('admin.insufficient_wallet_balance') ?? 'Insufficient wallet balance'], 400);
                }

                $user->decrement('wallet_balance', $amount);
            }

            $transaction->status = 'accepted';
            $transaction->save();

            // Optionally notify the user
            try {
                $transaction->user->notify(new \App\Notifications\NotifyAdmin([
                    'title' => __('admin.transaction_accepted') ?? 'Transaction accepted',
                    'body' => __('admin.your_transaction_has_been_accepted') ?? 'Your transaction has been accepted',
                    'type' => 'transaction_accepted',
                    'link' => url('/account/transactions'),
                ]));
            } catch (\Exception $e) {}

            DB::commit();
            return response()->json(['success' => true, 'message' => __('admin.transaction_accepted_successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reject a withdraw/transaction request
     */
    public function reject(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->status = 'rejected';
        $transaction->save();

        try {
            $transaction->user->notify(new \App\Notifications\NotifyAdmin([
                'title' => __('admin.transaction_rejected') ?? 'Transaction rejected',
                'body' => __('admin.your_transaction_has_been_rejected') ?? 'Your transaction has been rejected',
                'type' => 'transaction_rejected',
                'link' => url('/account/transactions'),
            ]));
        } catch (\Exception $e) {}

        return response()->json(['success' => true, 'message' => __('admin.transaction_rejected_successfully')]);
    }
}
