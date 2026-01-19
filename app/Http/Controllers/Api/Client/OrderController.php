<?php
namespace App\Http\Controllers\Api\Client;

use App\Http\Requests\Api\Order\ReportOrderProblemRequest;
use App\Http\Resources\Api\Client\LoyalityPointResource;
use App\Models\Order;
use App\Models\Problem;
use App\Models\Address;
use App\Services\Responder;
use App\Traits\ResponseTrait;
use App\Services\OrderService;
use App\Services\Order\OrderCheckoutService;
use App\Services\Order\OrderQueryService;
use App\Services\Order\OrderStatusService;
use App\Services\Order\DeliveryCalculationService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Requests\Api\Order\CancelOrderRequest;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Resources\Api\Client\OrderDetailsResource;
use App\Http\Resources\Api\Client\ClientPaymentResource;
use App\Notifications\OrderProblemReportedNotification;
use App\Http\Requests\CreateRefundRequest;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf as FacadesPdf;

class OrderController extends Controller
{
    use ResponseTrait;

    protected $orderService; // Keep for backward compatibility with some methods
    protected $checkoutService;
    protected $queryService;
    protected $statusService;
    protected $deliveryCalculationService;

    public function __construct(
        OrderService $orderService,
        OrderCheckoutService $checkoutService,
        OrderQueryService $queryService,
        OrderStatusService $statusService,
        DeliveryCalculationService $deliveryCalculationService
    ) {
        $this->orderService = $orderService;
        $this->checkoutService = $checkoutService;
        $this->queryService = $queryService;
        $this->statusService = $statusService;
        $this->deliveryCalculationService = $deliveryCalculationService;
    }

    /**
     * Get user orders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders()
    {
        $user = auth()->user();
        $filters = [
            'status' => request('status'),
            'sort_by' => request('sort_by'),
        ];
        $orders = $this->queryService->getUserOrders($user, $filters);

        return Responder::success(OrderResource::collection($orders));
    }

    /**
     * Get order details (non-refundable orders only)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrder($id)
    {
        $user = auth()->user();
        $order = $this->queryService->getUserOrder($user, $id);

        if (!$order) {
            return Responder::error(__('apis.order_not_found'), [], 404);
        }

        return Responder::success(new OrderDetailsResource($order));
    }

    /**
     * Get user refundable orders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRefundableOrders()
    {
        $user = auth()->user();
        $filters = [
            'status' => request('status'),
            'sort_by' => request('sort_by'),
        ];
        $orders = $this->queryService->getUserRefundableOrders($user, $filters);

        return Responder::success(OrderResource::collection($orders));
    }

    /**
     * Get refundable order details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRefundableOrder($id)
    {
        $user = auth()->user();
        $order = $this->queryService->getUserRefundableOrder($user, $id);

        if (!$order) {
            return Responder::error(__('apis.refund_order_not_found'), [], 404);
        }

        return Responder::success(new \App\Http\Resources\Api\Order\RefundOrderDetailsResource($order));
    }

    /**
     * Create a new order from the cart
     *
     * @param \App\Http\Requests\Api\Order\CreateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(CreateOrderRequest $request)
    {
        try {
            $user = auth()->user();

            // Pass origin for MyFatoorah callback routing
            $options = [
                'origin' => 'api-order',
            ];

            // Use the new checkout service for order creation
            $result = $this->checkoutService->createOrderFromCart($user, $request->validated(), $options);

            // If payment URL is present, return it with the order
            if (isset($result['payment_url']) && $result['payment_url']) {
                return Responder::success([
                    'message' => __('apis.order_created'),
                    'payment_url' => $result['payment_url'],
                    'order' => new OrderDetailsResource($result['order']),
                ]);
            }

            // For COD orders or if no payment URL
            return Responder::success([
                'message' => __('apis.order_created'),
                'order' => new OrderDetailsResource($result['order']),
            ]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

   

    public function reportProblem(ReportOrderProblemRequest $request)
{
    try {
        $user = auth()->user();

        $orderQuery = Order::where('id', $request->order_id);
        // Allow either the owning client or the assigned delivery to report the order
        if ($user->type === 'delivery') {
            $orderQuery->where('delivery_id', $user->id);
        } else {
            $orderQuery->where('user_id', $user->id);
        }

        $order = $orderQuery->firstOrFail();

        if ($order->status === 'problem') {
            return Responder::error(__('apis.problem_already_reported'), [], 400);
        }

        $order->update([
            'status' => 'problem',
            'problem_id' => $request->problem_id,
            'notes' => $request->notes,
        ]);

        $this->notifyAdminsOfProblem($order);

        return Responder::success(null, ['message' => __('apis.problem_reported_successfully')]);


    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return Responder::error(__('apis.order_not_found'), [], 404);
    } catch (\Exception $e) {
        return Responder::error(__('apis.failed_to_report_problem'), [], 500);
    }
}

    /**
     * Cancel an order
     *
     * @param \App\Http\Requests\Api\Order\CancelOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function SendCancelRequest(CancelOrderRequest $request)
     {
        try {
            $user = auth()->user();
            $orderQuery = Order::where('id', $request->order_id);
            // Allow either the owning client or the assigned delivery to report the order
            if ($user->type === 'delivery') {
                $orderQuery->where('delivery_id', $user->id);
            } else {
                $orderQuery->where('user_id', $user->id);
            }

            $order = $orderQuery->firstOrFail();

            // Check if order can be cancelled
            if (!in_array($order->status, ['processing', 'pending','new'])) {
                return Responder::error(__('apis.order_cannot_be_cancelled_at_this_stage'), [], 400);
            }

            // Check if order is already requested for cancellation
            if ($order->status === 'request_cancel') {
                return Responder::error(__('apis.cancellation_request_already_submitted'), [], 400);
            }

            // Update order status to request_cancel
            $order->update([
                'status' => 'request_cancel',
                'cancel_reason_id' => $request->cancel_reason_id,
                'notes' => $request->notes,
            ]);

            // Send notification to all admins about cancellation request
            $this->sendOrderCancellationNotificationToAdmins($order->id);

            return Responder::success(null, ['message' => __('apis.cancellation_request_submitted_successfully')]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Responder::error(__('apis.order_not_found'), [], 404);
        } catch (\Exception $e) {
            return Responder::error(__('apis.failed_to_submit_cancellation_request'), [], 500);
        }
     }

    public function payments()
    {
       $paymnets =  $this->orderService->ClientOnlinePayments()->get();
        return Responder::success(ClientPaymentResource::collection($paymnets));

    }

    public function loyalityPoints()
    {
        if(request()->has('earn')){
            $paymnets =  $this->orderService->ClientPayments()->where('loyalty_points_earned' , '>' , 0)->get();

        }else{
            $paymnets =  $this->orderService->ClientPayments()->where('loyalty_points_used' , '>' , 0)->get();

        }
        return Responder::success(['points' => auth()->user()->loyalty_points , 'list' => LoyalityPointResource::collection($paymnets)]);

    }

    public function downloadInvoice($orderId)
    {
        $user = auth()->user();
        $order = \App\Models\Order::with(['user', 'address', 'paymentMethod', 'items.item'])
            ->findOrFail($orderId);

        // Authorization: allow order owner (client) or assigned delivery only
        $isOwner    = ($order->user_id === ($user->id ?? null));
        $isDelivery = ($user && $user->type === 'delivery' && $order->delivery_id === $user->id);
        if (!$isOwner && !$isDelivery) {
            return Responder::error(__('apis.unauthorize'), [], 403);
        }

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

            'payment_method' => $order->paymentMethod,
        ];

        // Generate PDF
        $pdf = FacadesPdf::loadView('invoices.admin_order_invoice', $invoiceData, [], [
            'format'      => 'A4',
            'orientation' => 'portrait',
        ]);

        $filename = 'invoice_' .  now()->format('Ymd_His') . '.pdf';

        // Save to storage (public disk)
        \Storage::disk('public')->put('invoices/' . $filename, $pdf->output());

        // Generate the URL
        $downloadUrl = \Storage::url('invoices/' . $filename);

        return Responder::success([
            'url' => $downloadUrl,
            'filename' => $filename,
        ], __('apis.success'));
    }

    protected function sendNewOrderNotificationToAdmins($orderNum , $id)
    {
        $message = 'يوجد طلب حجز جديد برقم #' . $orderNum;
        $admins = \App\Models\Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'طلب جديد',
                    'en' => 'New Order'
                ],
                'body' => [
                    'ar' => $message,
                    'en' => $message
                ],
                'type' => 'new_order',
                'order_id' => $id
            ]));
        }
    }

    protected function sendOrderCancellationNotificationToAdmins($orderId)
    {
        $message = 'تم تقديم طلب إلغاء للطلب رقم #' . $orderId;
        $admins = \App\Models\Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'طلب إلغاء طلب',
                    'en' => 'Order Cancellation Request'
                ],
                'body' => [
                    'ar' => $message,
                    'en' => $message
                ],
                'type' => 'order_cancel_request',
                'order_id' => $orderId
            ]));
        }
    }


    /**
     * Calculate delivery fee
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateDeliveryFee(Request $request)
    {
        try {
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
            ]);

            // Simplified delivery fee calculation (fixed or from settings)
            $deliveryFee = (float) getSiteSetting('delivery_fee', 15);

            return Responder::success([
                'delivery_fee' => $deliveryFee,
                'distance_km' => 0,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return Responder::error(__('apis.validation_error'), $e->errors(), 422);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 500);
        }
    }

    protected function notifyAdminsOfProblem(Order $order)
{
        $problem = $order->problem_id ? Problem::find($order->problem_id) : null;
        $admins = \App\Models\Admin::all();

        // Fallback to notes when no problem record exists
        $reasonAr = $problem ? ($problem->problem ?? $order->notes ?? '') : ($order->notes ?? '');
        // If Problem has an English field try to use it, otherwise fallback to same text
        $reasonEn = $problem ? (($problem->problem_en ?? $problem->problem) ?? $order->notes ?? '') : ($order->notes ?? '');

        foreach ($admins as $admin) {
            $admin->notify(new OrderProblemReportedNotification([
                'title' => [
                    'ar' => 'تم التبليغ عن مشكلة في الطلب',
                    'en' => 'Problem Reported in Order'
                ],
                'body' => [
                    'ar' => "تم التبليغ عن مشكلة: {$reasonAr} في الطلب رقم #{$order->order_number}",
                    'en' => "A problem was reported: {$reasonEn} in order #{$order->order_number}"
                ],
                'type' => 'order_problem_reported',
                'order_id' => $order->id
            ]));
        }
}
    /**
     * Request a refund for an order
     */
    public function requestRefund(CreateRefundRequest $request)
    {
        try {
            $data = $request->validated();

            // Determine refund reason text with notes-first fallback
            $refundReasonId = $data['refund_reason_id'] ?? null;
            $notes = $data['notes'] ?? null;
            $reasonText = null;
            if (!empty($notes)) {
                $reasonText = $notes;
            } elseif (!empty($refundReasonId)) {
                $refundReason = \App\Models\RefundReason::find($refundReasonId);
                $reasonText = $refundReason ? $refundReason->reason : null;
            }

            // Get product IDs from items array
            $productIds = $data['items'];

            // Get images if provided
            $images = $request->hasFile('images') ? $request->file('images') : [];

            // Use new RefundService
            $refundService = app(\App\Services\Order\RefundService::class);
            $order = $refundService->requestRefund(
                $data['order_id'],
                $productIds,
                $refundReasonId,
                $reasonText,
                $images,
                $notes
            );

            // Send notification to all admins
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\RefundRequestNotification($order));
            }

            return Responder::success(
                (new \App\Http\Resources\Api\Client\OrderDetailsResource($order))->forDeliveryRefund(true),
                ['message' => __('apis.refund_request_submitted_successfully')]
            );

        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 400);
        }
    }
}
