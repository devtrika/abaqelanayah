<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Facades\Responder;
use App\Http\Resources\Api\Client\OrderDetailsResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Models\Order;

use App\Services\OrderService;
use App\Services\Order\OrderQueryService;
use App\Services\Order\OrderStatusService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $queryService;
    protected $statusService;

    public function __construct(
        OrderQueryService $queryService,
        OrderStatusService $statusService
    ) {
        $this->queryService = $queryService;
        $this->statusService = $statusService;
    }

    /**
     * Get delivery person's orders
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $orders = $this->queryService->getDeliveryOrders(
            $request->get('status'),
            $request->get('search')
        );

        // Use unified OrderResource
        return Responder::success(OrderResource::collection($orders));
    }

    /**
     * Show delivery order details
     */
    public function show($id)
    {
        $user = auth()->user();
        if ($user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $order = $this->queryService->getDeliveryOrder($id);

        if (!$order) {
            return Responder::error(__('apis.order_not_found'), 404);
        }

        return Responder::success(new OrderDetailsResource($order));
    }

    /**
     * Update order status (delivery person)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        $user = auth()->user();
        if ($user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        try {
            $validated = $request->validated();
            $order = $this->statusService->updateDeliveryOrderStatus($id, $validated['status']);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), 422);
        }

        if ($order === null) {
            return Responder::error(__('apis.order_not_found'), 404);
        }

        if ($order === false) {
            return Responder::error(__('apis.invalid_status_update'), 422);
        }

        return Responder::success([
            'message' => __('apis.order_status_updated'),
            'order'   => new OrderResource($order),
        ]);
    }


    public function statistics()
    {
        $user = auth()->user();
        if ($user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $query = Order::where('delivery_id', $user->id)
            ->where(function ($q) {
                $q->where('is_refund', false)->orWhereNull('is_refund');
            });

        // Pending orders (scoped to this delivery)
        $pendingOrders  =  (clone $query)->where('status', 'pending')->count();
        // Total profit (delivered + payment success)
        $totalProfitEarned = (clone $query)
            ->where('status', 'delivered')
            ->where('payment_status', \App\Enums\PaymentStatus::SUCCESS->value)
            ->sum('total');

        // Today's profit (delivered + payment success + created today)
        $todayProfitEarned = (clone $query)
            ->where('status', 'delivered')
            ->where('payment_status', \App\Enums\PaymentStatus::SUCCESS->value)
            ->whereDate('updated_at', today())
            ->sum('total');

        // Total new orders
        $totalNewOrders = (clone $query)
            ->where('status', 'new')
            ->count();

        // Total new orders
        $totalProcessingOrders = (clone $query)
            ->where('status', 'confirmed')
            ->count();

          // Total new orders
    $totalDeliveredOrders = (clone $query)
        ->where('status', 'delivered')
        ->count();

        // Refund orders are normal orders flagged with is_refund = true and assigned to this delivery user
        $refundBaseQuery = Order::query()
            ->where('delivery_id', $user->id)
            ->where('refundable', true);

        // New refund pickups awaiting action
        $refundsNew = (clone $refundBaseQuery)
            ->where('status', \App\Enums\OrderStatus::NEW->value)
            ->count();

        // In progress: out-for-delivery
        $refundsInProgress = (clone $refundBaseQuery)
            ->where('status', \App\Enums\OrderStatus::OUT_OF_DELIVERY->value)
            ->count();

        // Finished: refunded OR request_rejected
        $refundsFinished = (clone $refundBaseQuery)
            ->whereIn('status', [
                \App\Enums\OrderStatus::REFUNDED->value,
                \App\Enums\OrderStatus::REQUEST_REJECTED->value,
            ])->count();

        $data = [
            'total_profit' => (int) $totalProfitEarned,
            'today_profit' => (int) $todayProfitEarned,
            'pending_orders' => $pendingOrders,
            'new_orders' => $totalNewOrders,
            'processing_orders' => $totalProcessingOrders,
            'delivered_orders' => $totalDeliveredOrders,
            'refunds_new' => $refundsNew,
            'refunds_in_progress' => $refundsInProgress,
            'refunds_finished' => $refundsFinished,
        ];

        return Responder::success($data);
    }

}

