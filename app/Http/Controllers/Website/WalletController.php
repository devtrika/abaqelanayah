<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\Paymob\PaymobService;
use App\Http\Requests\CreateWithdrawRequest;
use App\Http\Requests\CreateWalletRechargeRequest;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected PaymobService $paymobService
    ) {}

    /**
     * Display wallet page with transactions
     */
    public function index()
    {
        $user = Auth::guard('web')->user();
        
        // Get wallet balance
        $balance = $user->wallet_balance;
        
        // Get all transactions
        $allTransactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->get();
        
        // Filter transactions by type
        $paymentTransactions = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet-payment')
            ->latest()
            ->get();
            
        $addonTransactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['wallet-addons', 'wallet-recharge'])
            ->latest()
            ->get();
            
        $withdrawTransactions = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet-withdraw')
            ->latest()
            ->get();
        
        // New logic per product requirement:
        // - Withdrawable balance: current wallet available for new withdraw requests
        //   equals wallet_balance minus any amounts already requested and pending approval.
        // - Non-withdrawable balance: sum of pending withdraw requests awaiting admin approval.
        // - Total balance card displays wallet_balance, which equals (withdrawable + pending).

        $pendingWithdrawSum = Transaction::where('user_id', $user->id)
            ->where('type', 'wallet-withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        $withdrawableBalance = max($balance - $pendingWithdrawSum, 0);
        $nonWithdrawableBalance = max($pendingWithdrawSum, 0);
        
        return view('website.pages.account.wallet', compact(
            'balance',
            'withdrawableBalance',
            'nonWithdrawableBalance',
            'paymentTransactions',
            'addonTransactions',
            'withdrawTransactions'
        ));
    }

    /**
     * Create withdraw request
     */
    public function createWithdrawRequest(CreateWithdrawRequest $request)
    {
        $data = $request->validated();

        try {
            $this->transactionService->createWithdrawRequest($data);

            return redirect()->route('website.wallet.index')
                ->with('success', __('site.withdraw_request_success'));
        } catch (\Exception $e) {
            return redirect()->route('website.wallet.index')
                ->with('error', __('site.withdraw_request_error'));
        }
    }

    /**
     * Create wallet recharge request with Paymob
     */
    public function createRechargeRequest(CreateWalletRechargeRequest $request)
    {
        $data = $request->validated();
        $user = Auth::guard('web')->user();

        try {
            // Create pending transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'type' => 'wallet-addons',
                'status' => 'pending',
                'reference' => 'pending',
                'note' => json_encode(value: ['ar' => 'شحن محفظة', 'en' => 'Wallet Recharge']),
            ]);

            $result = $this->paymobService->createWalletRechargePayment($transaction, 'wallet-deposit');

            // Update transaction reference and redirect
            $transaction->update(['reference' => $result['merchant_order_id']]);
            return redirect($result['payment_url']);
        } catch (\Exception $e) {
            return redirect()->route('website.wallet.index')
                ->with('error', 'حدث خطأ أثناء تهيئة الدفع. يرجى المحاولة مرة أخرى');
        }
    }
}

