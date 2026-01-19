<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function RevenueReport()
    {
        if (request()->ajax()) {
            $orders = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod'])
                ->search(request()->searchArray)
                ->paginate(30);
            $html = view('admin.reports.revenue-report.table', compact('orders'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.reports.revenue-report.index');
    }

    public function wallets()
    {
        if (request()->ajax()) {
            $walletTransactions = WalletTransaction::whereNull('provider_id')->
                with(['user', 'provider'])
                ->search(request()->searchArray)
                ->paginate(30);
            $html = view('admin.reports.wallet-transactions.table', compact('walletTransactions'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.reports.wallet-transactions.index');
    }

    public function exportRevenueReport(Request $request)
    {
        $filters = [
            'from_date' => $request->input('from_date'),
            'to_date'   => $request->input('to_date'),
        ];
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RevenueReportExport($filters),
            'revenue-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function PaymentReport()
    {
        if (request()->ajax()) {
            $orders = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod'])
                ->search(request()->searchArray)
                ->paginate(30);
            $html = view('admin.reports.payment-report.table', compact('orders'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.reports.payment-report.index');
    }

    public function exportPaymentReport(Request $request)
    {
        $filters = [
            'from_date' => $request->input('from_date'),
            'to_date'   => $request->input('to_date'),
        ];
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PaymentReportExport($filters),
            'payment-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadOrderInvoicePdf($orderId)
    {
        // Get the order with all relationships
        $order = \App\Models\Order::with(['user', 'address', 'paymentMethod', 'items.item'])
            ->findOrFail($orderId);

        // Prepare invoice data
        $invoiceData = [
            'order'          => $order,
            'customer'       => $order->user,
            'items'          => $order->items,
            'totals'         => [
                'products_total'    => (float) $order->subtotal,
                'amount'            => (float) $order->discount_amount,

                'delivery_fee'      => (float) $order->delivery_fee,
                'wallet_deduction'  => $order->wallet_deduction,
                'loyalty_deduction' => $order->loyalty_deduction,

                'final_total'       => (float) $order->total,
            ],
            'address'        => $order->address,
            // 'branch'         => $order->branch,

            'payment_method' => $order->paymentMethod,
        ];

        // Generate PDF
        $pdf = PDF::loadView('invoices.admin_order_invoice', $invoiceData, [], [
            'format'      => 'A4',
            'orientation' => 'portrait',
        ]);

        $filename = 'invoice_' . $order->order_number . '_' . now()->format('Ymd_His') . '.pdf';

        // Save to storage (public disk)
        \Storage::disk('public')->put('invoices/' . $filename, $pdf->output());

        // Generate the URL
        $downloadUrl = \Storage::url('invoices/' . $filename);

        return $pdf->download($filename);

    }

    public function WithdrawRequestsReport()
    {
        $providers = \App\Models\Provider::with('user')->get()->mapWithKeys(function ($provider) {
            return [$provider->id => optional($provider->user)->name ?: $provider->commercial_name];
        });
        if (request()->ajax()) {
            $withdrawRequests = WithdrawRequest::with(['provider'])
                ->search(request()->searchArray)

                ->paginate(30);
            $html = view('admin.reports.withdraw-requests-report.table', compact('withdrawRequests'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.reports.withdraw-requests-report.index', compact('providers'));
    }

    public function acceptWithdrawRequest(Request $request)
    {
        $request->validate([
            'withdraw_request_id' => 'required|exists:withdraw_requests,id',
            'amount'              => 'required|numeric|min:0.01',
            'image'               => 'nullable|image',
        ]);
        $withdrawRequest = \App\Models\WithdrawRequest::findOrFail($request->withdraw_request_id);
        $provider        = $withdrawRequest->provider;
        if (! $provider) {
            return response()->json(['success' => false, 'message' => __('admin.provider_not_found')], 404);
        }
        // Check wallet balance
        if ($provider->wallet_balance < $request->amount) {
            return response()->json(['success' => false, 'message' => __('admin.insufficient_wallet_balance')], 400);
        }
        // Deduct from wallet
        $provider->decrement('wallet_balance', $request->amount);
        $provider->increment('withdrawable_balance', $request->amount);

        // Log wallet transaction

        \App\Models\WalletTransaction::create([
            'provider_id' => $provider->id,
            'amount'      => -abs($request->amount),
            'user_id'     => $provider->user_id,
            'type'        => 'withdraw',
            'status'      => 'completed',
            'reference'   => 'withdraw_admin_' . $withdrawRequest->id,
        ]);
        $withdrawRequest->status = 'accepted';
        $withdrawRequest->amount = $request->amount;
        if ($request->hasFile('image')) {
            $withdrawRequest->addMedia($request->file('image'))->toMediaCollection('withdraw_requests');
        }
        $withdrawRequest->save();
        // Notify provider's user
        $providerUser = optional($provider)->user;
        if ($providerUser) {
            $providerUser->notify(new \App\Notifications\NotifyUser([
                'title' => __('admin.withdraw_request_accepted'),
                'body'  => __('admin.withdraw_request_accepted_body'),
                'type'  => 'withdraw_request',
            ]));
        }
        return response()->json(['success' => true, 'message' => __('admin.withdraw_request_accepted')]);
    }

    public function rejectWithdrawRequest(Request $request)
    {
        $request->validate([
            'withdraw_request_id' => 'required|exists:withdraw_requests,id',
        ]);
        $withdrawRequest         = \App\Models\WithdrawRequest::findOrFail($request->withdraw_request_id);
        $withdrawRequest->status = 'rejected';
        $withdrawRequest->save();
        // Notify provider's user
        $providerUser = optional($withdrawRequest->provider)->user;
        if ($providerUser) {
            $providerUser->notify(new \App\Notifications\NotifyUser([
                'title' => __('admin.withdraw_request_rejected'),
                'body'  => __('admin.withdraw_request_rejected_body'),
                'type'  => 'withdraw_request',
            ]));
        }
        return response()->json(['success' => true, 'message' => __('admin.withdraw_request_rejected')]);
    }

    public function exportWithdrawRequests(Request $request)
    {
        $query = \App\Models\WithdrawRequest::with(['provider', 'provider.user', 'provider.bankAccount']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $withdrawRequests = $query->latest()->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\WithdrawRequestsExport($withdrawRequests),
            'withdraw-requests-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function CommissionReport()
    {
        $providers = \App\Models\Provider::with('user')->get()->mapWithKeys(function ($provider) {
            return [$provider->id => optional($provider->user)->name ?: $provider->commercial_name];
        });
        if (request()->ajax()) {
            $orders = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod'])
                ->search(request()->searchArray)
                ->paginate(30);
            // Calculate sums for the current page
            $totalCommission = $orders->sum('platform_commission');
            $totalBookingFee = $orders->sum('booking_fee');
            $totalCancelFees = $orders->sum('cancel_fees');
            $html            = view('admin.reports.commission-reports.table', compact('orders', 'totalCommission', 'totalBookingFee', 'totalCancelFees'))->render();
            return response()->json(['html' => $html]);
        }
        // For the full (unpaginated) set, you may want to sum all filtered orders
        $ordersQuery     = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod']);
        $totalCommission = $ordersQuery->sum('platform_commission');
        $totalBookingFee = $ordersQuery->sum('booking_fee');
        $totalCancelFees = $ordersQuery->sum('cancel_fees');
        return view('admin.reports.commission-reports.index', compact('providers', 'totalCommission', 'totalBookingFee', 'totalCancelFees'));
    }

    public function exportCommissionReport(Request $request)
    {
        $query = Order::with(['user', 'provider', 'address', 'bankTransfer', 'paymentMethod']);

        // Apply filters if present (date, provider, etc.)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        // Add more filters as needed

        $orders = $query->latest()->get();

        $exportData = $orders->map(function ($order) {
            return [
                'order_number'        => $order->order_number,
                'total'               => $order->total,
                'created_at'          => $order->created_at->format('Y-m-d H:i'),
                'platform_commission' => $order->platform_commission,
                'booking_fee'         => $order->booking_fee,
                'cancel_fees'         => $order->cancel_fees,
            ];
        });

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CommissionReportExport($exportData),
            'commission-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

}
