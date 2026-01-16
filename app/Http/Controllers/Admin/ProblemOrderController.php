<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\SiteSetting;
use App\Services\WalletService;
use App\Traits\Report;
use App\Services\TransactionService;
use App\Enums\OrderStatus as OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProblemOrderController extends Controller
{
    protected $transactionService;
    protected $walletService;

    public function __construct(TransactionService $transactionService , WalletService $walletService)
    {
        $this->transactionService = $transactionService;
        $this->walletService = $walletService;
    }

    /**
     * Display a listing of cancel request orders.
     */
    public function index($id = null)
    {
        if (request()->ajax()) {
            $orders = Order::with(['user', 'address', 'cancelReason', 'paymentMethod'])
                ->where('status', 'problem')
                ->search(request()->searchArray)
                ->paginate(30);
            $html = view('admin.problem_orders.table', compact('orders'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.problem_orders.index');
    }

    /**
     * Display the specified cancel request order.
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.item',
            'coupon',
            'cancelReason',
           
    ])->where('status', 'problem')->findOrFail($id);

        // Get payment method details
        $paymentMethod = \App\Models\PaymentMethod::find($order->payment_method_id);

        // Get cancellation fee settings
        $cancellationFeeAmount = SiteSetting::where('key', 'cancellation_fee_amount')->value('value') ?? 5.00;
        $cancellationFeePercentage = SiteSetting::where('key', 'cancellation_fee_percentage')->value('value') ?? 0;

        return view('admin.problem_orders.show', compact('order', 'paymentMethod', 'cancellationFeeAmount', 'cancellationFeePercentage'));
    }

    /**
     * Accept cancel request
     */
    public function acceptCancelRequest(Request $request, $id)
    {
        $request->validate([
            'cancel_fees' => 'required|numeric|min:0'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $order = Order::with(['user'])->findOrFail($id);

                if ($order->status !== 'problem') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order is not in problem status'
                    ], 400);
                }

                $cancelFees = (float) $request->cancel_fees;

                // Calculate refund amount (total - cancel fees)
                $refundAmount = max(0, $order->total - $cancelFees);

                // Update main order status
                $order->update([
                    'cancel_fees' => $cancelFees,
                    'status' => OrderStatusEnum::CANCELLED->value,
                ]);

            

                // Create main order status record
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => OrderStatusEnum::CANCELLED->value,
                    'map_desc' => "Cancel request accepted by admin. Refund: {$refundAmount}, Fees: {$cancelFees}",
                ]);

                // Process refund if amount > 0
                if ($refundAmount > 0) {
                    $this->processRefund($order, $refundAmount);
                }

                // Restore product quantities
                $this->restoreProductQuantities($order);
                // Log the action
                Report::addToLog("قبول طلب إلغاء الطلب رقم {$order->order_number}. المبلغ المسترد: {$refundAmount}, رسوم الإلغاء: {$cancelFees}");

                // Send notification to the order user
                $order->user->notify(new \App\Notifications\NotifyUser([
                    'title' => [
                        'ar' => 'قبول طلب الإلغاء',
                        'en' => 'Cancel Request Accepted'
                    ],
                    'body' => [
                        'ar' => 'تم قبول طلب الإلغاء للطلب رقم ' . $order->order_number . '. سيتم إعادة المبلغ المسترد إلى محفظتك.',
                        'en' => 'Your cancel request for order #' . $order->order_number . ' has been accepted. The refund will be returned to your wallet.'
                    ],
                    'type' => 'order_cancel_accepted',
                    'order_id' => $order->id
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Cancel request accepted successfully',
                    'data' => [
                        'refund_amount' => $refundAmount,
                        'cancel_fees' => $cancelFees
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept cancel request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject cancel request
     */
    public function rejectCancelRequest(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $order = Order::findOrFail($id);

                if ($order->status !== 'problem') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order is not in problem status'
                    ], 400);
                }

                $reason = $request->reason;

                // Update order status back to previous status (usually processing)
                $order->update([
                    'status' => OrderStatusEnum::PROCESSING->value,
                ]);

                // Create main order status record
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => OrderStatusEnum::PROCESSING->value,
                    'map_desc' => "Cancel request rejected by admin: {$reason}",
                ]);

                // Log the action
                Report::addToLog("رفض طلب إلغاء الطلب رقم {$order->order_number}: {$reason}");

                // Send notification to the order user
                $order->user->notify(new \App\Notifications\NotifyUser([
                    'title' => [
                        'ar' => 'رفض طلب الإلغاء',
                        'en' => 'Cancel Request Rejected'
                    ],
                    'body' => [
                        'ar' => 'تم رفض طلب الإلغاء للطلب رقم ' . $order->order_number . '. السبب: ' . $reason,
                        'en' => 'Your cancel request for order #' . $order->order_number . ' was rejected. Reason: ' . $reason
                    ],
                    'type' => 'order_cancel_rejected',
                    'order_id' => $order->id
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Cancel request rejected successfully'
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject cancel request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund based on payment method
     */
    private function processRefund(Order $order, float $refundAmount)
    {
       $wallet = $order->user->wallet_balance;
       $order->user->update([ 'wallet_balance' => $wallet + $refundAmount ]);
       $this->transactionService->createWalletRefundTransaction($order->user_id ,$refundAmount , $order->order_number );
       
    }

    /**
     * Restore product quantities when order is cancelled
     */
    private function restoreProductQuantities(Order $order)
    {
        foreach ($order->items as $orderItem) {
            if ($orderItem->product_id) {
                $product = \App\Models\Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('quantity', $orderItem->quantity);
                }
            }
        }
    }
}
