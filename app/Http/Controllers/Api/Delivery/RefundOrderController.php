<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Facades\Responder;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\Order\RefundOrderDetailsResource;
use App\Http\Resources\Api\Order\RefundOrderIndexResource;
use Illuminate\Http\Request;
use App\Services\Order\RefundService;
use App\Models\Order;

class RefundOrderController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * List refund orders (API)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $filters = $request->all();
        $refundOrders = $this->refundService->getRefundOrdersForDelivery($filters);

        // Use unified OrderResource with delivery refund flag
        $resources = RefundOrderIndexResource::collection($refundOrders)->map(function($resource) {
            return $resource->forDeliveryRefund(true);
        });

        return Responder::success($resources);
    }

    /**
     * Show single refund order
     */
    public function show($id)
    {
        $user = auth()->user();
        if (!$user || $user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $refundOrder = Order::where('refundable', true)
            ->with([
                'user', 
                'items.product',
                'items.weightOption',
                'items.cuttingOption',
                'items.packagingOption',
                'address',
                'address.city',
                'address.district',
                'city',  // For gift orders without address
                'delivery',
                'refundReason'
            ])
            ->find($id);

        if (!$refundOrder) {
            return Responder::error(__('apis.refund_order_not_found'), [], 404);
        }

        // Ensure this refund belongs to the authenticated delivery
        if ($refundOrder->delivery_id != $user->id) {
            return Responder::error(__('apis.forbidden'), 403);
        }

        // Return dedicated RefundOrderDetailsResource
        return Responder::success(new RefundOrderDetailsResource($refundOrder));
    }

    /**
     * Update refund order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,out-for-delivery,delivered',
        ]);

        $user = auth()->user();
        if (!$user || $user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        try {
            $refundOrder = $this->refundService->updateRefundOrderStatus($id, $request->status);

            // Return dedicated RefundOrderDetailsResource
            return Responder::success(
                new RefundOrderDetailsResource($refundOrder),
                ['message' => __('apis.status_updated_successfully')]
            );
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 400);
        }
    }
}
