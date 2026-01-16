<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundService
{
    protected $orderRepository;
    protected $paymentService;
    protected $notificationService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderPaymentService $paymentService,
        OrderNotificationService $notificationService
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Request a refund for specific products in an order
     *
     * @param int $orderId
     * @param array $productIds Array of product IDs to refund (full quantity)
     * @param int|null $refundReasonId
     * @param string|null $reasonText
     * @param array $images
     * @param string|null $notes
     * @return Order The original order (updated with refund request status)
     * @throws \Exception
     */
    public function requestRefund(
        int $orderId,
        array $productIds,
        ?int $refundReasonId,
        ?string $reasonText = null,
        array $images = [],
        ?string $notes = null
    ): Order {
        return DB::transaction(function () use ($orderId, $productIds, $refundReasonId, $reasonText, $images, $notes) {
            $order = Order::with('items.product')->findOrFail($orderId);

            // Check if user owns this order
            if ($order->user_id !== auth()->id()) {
                throw new \Exception(__('admin.unauthorized_action'));
            }

            // // Check if order can be refunded
            // if ($order->is_refund) {
            //     throw new \Exception(__('apis.cannot_refund_a_refund_order'));
            // }

            // Main order cannot be refunded if cancelled; completed refunds tracked via refund_status
            if ($order->status === 'cancelled' || in_array($order->refund_status, ['refunded', 'request_rejected'])) {
                throw new \Exception(__('admin.order_cannot_be_refunded'));
            }

            // Validate products
            if (empty($productIds)) {
                throw new \Exception(__('apis.no_items_selected_for_refund'));
            }

            $totalRefundAmount = 0;
            $refundItems = [];

            foreach ($productIds as $productId) {
                // Find order item by product_id
                $orderItem = $order->items->firstWhere('product_id', $productId);

                if (!$orderItem) {
                    throw new \Exception(__('apis.product_not_in_order'));
                }

                // Calculate available quantity (original - already refunded)
                $alreadyRefunded = $orderItem->refund_quantity;
                $availableQuantity = $orderItem->quantity - $alreadyRefunded;

                if ($availableQuantity <= 0) {
                    throw new \Exception(__('apis.product_already_fully_refunded', [
                        'product' => $orderItem->product->name ?? 'Product'
                    ]));
                }

                // Refund the full available quantity
                $refundQuantity = $availableQuantity;

                // Calculate refund amount for this item
                $itemUnitPrice = $orderItem->total / $orderItem->quantity;
                $itemRefundAmount = $itemUnitPrice * $refundQuantity;

                $refundItems[] = [
                    'order_item' => $orderItem,
                    'refund_quantity' => $refundQuantity,
                    'refund_amount' => $itemRefundAmount,
                ];

                $totalRefundAmount += $itemRefundAmount;
            }

            // Update refund status to request_refund (do not change main status)
            $updateData = [
                'refund_status' => 'request_refund',
                'refundable' => true,
                'refund_requested_at' => now(),
                'refund_reason_id' => $refundReasonId,
                'refund_reason_text' => $reasonText,
                'refund_amount' => $totalRefundAmount,
            ];
            if ($notes !== null) {
                $updateData['notes'] = $notes;
            }
            $order->update($updateData);

            // Mark items as pending refund (set request_refund flag)
            foreach ($refundItems as $refundItem) {
                $refundItem['order_item']->update([
                    'request_refund' => true,
                ]);
                
                Log::info('Refund requested for order item', [
                    'order_id' => $order->id,
                    'order_item_id' => $refundItem['order_item']->id,
                    'refund_quantity' => $refundItem['refund_quantity'],
                    'refund_amount' => $refundItem['refund_amount'],
                ]);
            }

            // // Handle images if provided
            // if (!empty($images)) {
            //     foreach ($images as $image) {
            //         $order->addMedia($image)->toMediaCollection('refund_images');
            //     }
            // }

            // Send notification to admin
            // TODO: Implement admin notification

            return $order;
        });
    }

    /**
     * Approve a refund request (NO new order created, just update original)
     *
     * @param int $orderId
     * @param array $items Array of ['order_item_id' => quantity_to_refund]
     * @param float $refundAmount
     * @param int|null $deliveryId
     * @return Order The original order (updated)
     * @throws \Exception
     */
    public function approveRefund(
        int $orderId,
        array $items,
        float $refundAmount,
        ?int $deliveryId = null
    ): Order {
        return DB::transaction(function () use ($orderId, $items, $refundAmount, $deliveryId) {
            $order = Order::with('items', 'user')->findOrFail($orderId);

            // Validate order status
            if ($order->refund_status !== 'request_refund') {
                throw new \Exception(__('apis.order_not_in_refund_request_status'));
            }

            // Generate refund number if not exists
            $refundNumber = $order->refund_number;
            if (!$refundNumber) {
                $refundNumber = Order::generateRefundNumber();
            }

            // Update order items - mark which items are refunded
            foreach ($items as $orderItemId => $refundQuantity) {
                $orderItem = $order->items()->find($orderItemId);
                if ($orderItem) {
                    $itemUnitPrice = $orderItem->total / $orderItem->quantity;
                    $itemRefundAmount = $itemUnitPrice * $refundQuantity;

                    $orderItem->update([
                        'is_refunded' => true,
                        'refund_quantity' => $orderItem->refund_quantity + $refundQuantity,
                        'refund_amount' => $orderItem->refund_amount + $itemRefundAmount,
                    ]);
                }
            }
            // Set refund_status to 'new' and assign delivery person
            // Payment will be processed AFTER delivery confirms pickup (refund_status = delivered)
            $order->update([
                'refund_status' => 'new',
                'refund_number' => $refundNumber,
                'refund_approved_at' => now(),
                'refund_approved_by' => auth('admin')->id(),
                'refund_amount' => $refundAmount,
                'delivery_id' => $deliveryId, // Assign delivery person for pickup
                'is_refund' => true, // Mark as refund order
            ]);

            // Notify assigned delivery (refund pickup)
            if ($deliveryId) {
                $this->notificationService->notifyDeliveryOfRefundAssignment(
                    $order->fresh(['delivery', 'address'])
                );
            }

            // Send notification to user about refund approval (not payment yet)
            $this->notificationService->notifyUserOfRefund($order, $refundAmount);

            Log::info('Refund approved and assigned to delivery', [
                'order_id' => $order->id,
                'refund_amount' => $refundAmount,
                'delivery_id' => $deliveryId,
                'status' => 'new',
            ]);

            return $order->fresh();
        });
    }

    /**
     * Reject a refund request
     *
     * @param int $orderId
     * @param string|null $rejectionReason
     * @return Order
     * @throws \Exception
     */
    public function rejectRefund(int $orderId, ?string $rejectionReason = null): Order
    {
        return DB::transaction(function () use ($orderId, $rejectionReason) {
            $order = Order::findOrFail($orderId);

            if ($order->refund_status !== 'request_refund') {
                throw new \Exception(__('apis.order_not_in_refund_request_status'));
            }

            // Ensure refund_number exists for tracking even when rejected
            $refundNumber = $order->refund_number;
            if (!$refundNumber) {
                $refundNumber = Order::generateRefundNumber();
            }

            $order->update([
                'refund_status' => 'request_rejected',
                'refund_number' => $refundNumber,
                'refund_rejected_at' => now(),
                'refund_rejected_by' => auth('admin')->id(),
                'refund_reason_text' => $rejectionReason ?? $order->refund_reason_text,
                'refundable' => false,
            ]);

            // Send notification to user
            // TODO: Implement rejection notification

            return $order->fresh();
        });
    }

    /**
     * Process refund payment to user
     *
     * @param Order $order
     * @return void
     */
    protected function processRefundPayment(Order $order): void
    {
        // Refund to wallet
        $this->paymentService->refundToWallet($order);

        Log::info('Refund payment processed', [
            'order_id' => $order->id,
            'refund_number' => $order->refund_number,
            'amount' => $order->refund_amount,
        ]);
    }

    /**
     * Get refund orders for delivery (orders with refundable flag)
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRefundOrdersForDelivery(array $filters = [])
    {
        $user = auth()->user();

        $query = Order::with(['user', 'items' => function($q) {
                // Only load items requested for refund
                $q->where('request_refund', true);
            }, 'items.product', 'delivery', 'refundReason'])
            ->where('refundable', true);

        if ($user && $user->type === 'delivery') {
            $query->where('delivery_id', $user->id);
        }

        // Apply filters
        if (isset($filters['refund_number'])) {
            $query->where('refund_number', 'like', "%{$filters['refund_number']}%");
        }

        if (isset($filters['order_number'])) {
            $query->where('order_number', 'like', "%{$filters['order_number']}%");
        }

        // Filter by high-level refund status groups for delivery app
        if (isset($filters['status'])) {
            $status = strtolower((string) $filters['status']);

            // Map UI statuses to order refund_status values
            if ($status === 'new') {
                $query->where('refund_status', 'new');
            } elseif ($status === 'out-for-delivery') {
                $query->where('refund_status', 'out-for-delivery');
            } elseif ($status === 'finished') {
                // Finished includes refused (request_rejected) and refunded
                $query->whereIn('refund_status', ['refunded', 'request_rejected','delivered']);
            }
        }

        return $query->latest('created_at')->get();
    }

    /**
     * Update refund order status (for delivery)
     *
     * @param int $orderId
     * @param string $status
     * @return Order
     * @throws \Exception
     */
    public function updateRefundOrderStatus(int $orderId, string $status): Order
    {
        return DB::transaction(function () use ($orderId, $status) {
            $order = Order::where('refundable', true)->findOrFail($orderId);

            $user = auth()->user();
            if ($user && $user->type === 'delivery' && $order->delivery_id !== $user->id) {
                throw new \Exception(__('apis.unauthorized_action'));
            }

            // Validate status transitions for refund orders
            $validTransitions = [
                'out-for-delivery' => ['out-for-delivery'],
                'delivered' => ['delivered'],

            ];

            $currentStatus = $order->refund_status;
      

            // Update refund_status (do not change main status)
            $order->update([
                'refund_status' => $status,
            ]);

            // Process refund payment when items are delivered (picked up)
            if ($status === 'delivered') {
                // Change refund_status to refunded after delivery confirms pickup
                $order->update([
                'refund_status' => $status,
                ]);

                // Process refund payment to user's wallet
                $this->processRefundPayment($order);

                // Update refunded items
                foreach ($order->items->where('request_refund', true) as $item) {
                    $item->update([
                        'is_refunded' => true,
                    ]);
                }

                Log::info('Refund payment processed after delivery pickup', [
                    'order_id' => $order->id,
                    'refund_number' => $order->refund_number,
                    'refund_amount' => $order->refund_amount,
                    'user_id' => $order->user_id,
                ]);

                // Notify user about refund completion
                // TODO: Implement notifyUserOfRefundCompletion in OrderNotificationService
                // $this->notificationService->notifyUserOfRefundCompletion($order);
            }

            Log::info('Refund order status updated', [
                'order_id' => $order->id,
                'refund_number' => $order->refund_number,
                'old_status' => $currentStatus,
                'new_status' => $status,
                'final_refund_status' => $order->fresh()->refund_status,
                'delivery_id' => $user->id,
            ]);

            return $order->fresh();
        });
    }
}

