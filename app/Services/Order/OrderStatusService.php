<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * OrderStatusService
 *
 * Handles order status transitions and validations
 * Uses repositories for all database operations
 */
class OrderStatusService
{
    protected $orderRepository;
    protected $inventoryService;
    protected $notificationService;
    protected $paymentService;

    public function __construct(
        OrderRepository $orderRepository,
        InventoryService $inventoryService,
        OrderNotificationService $notificationService,
        OrderPaymentService $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->inventoryService = $inventoryService;
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
    }

    /**
     * Validate if a status transition is allowed
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        $allowedTransitions = [
            'pending' => ['new', 'cancelled'],
            'new' => ['confirmed', 'cancelled', 'problem'],
            'confirmed' => ['delivered', 'cancelled', 'problem'],
            'delivered' => ['request_refund'],
            'problem' => ['cancelled', 'new'],
            'cancelled' => ['refunded'],
            'request_refund' => ['refunded', 'cancelled'],
            'refunded' => [],
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    /**
     * Update order status with validation
     *
     * @param Order $order
     * @param string $newStatus
     * @param array $options ['user_type' => 'admin'|'delivery', 'notify' => true]
     * @return Order
     * @throws \Exception
     */
    public function updateStatus(Order $order, string $newStatus, array $options = []): Order
    {
        return $this->orderRepository->transaction(function () use ($order, $newStatus, $options) {
            // Validate transition unless admin is forcing override
            $userType = $options['user_type'] ?? null;
            $bypassValidation = ($userType === 'admin');

            if (!$bypassValidation && !$this->isValidTransition($order->status, $newStatus)) {
                throw new \Exception(__('admin.invalid_status_transition'));
            }

            $oldStatus = $order->status;

            // Update status using repository
            $this->orderRepository->update($order, ['status' => $newStatus]);

            // Handle side effects
            $this->handleStatusSideEffects($order, $oldStatus, $newStatus);

            // Send notification if enabled
            if ($options['notify'] ?? true) {
                $this->notificationService->notifyUserOfStatusChange($order, $newStatus);
            }

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => $options['user_type'] ?? 'system',
            ]);

            return $order->fresh();
        });
    }

    /**
     * Cancel order with reason
     *
     * @param Order $order
     * @param string|null $reason
     * @param mixed $cancelledBy
     * @return Order
     * @throws \Exception
     */
    public function cancelOrder(Order $order, ?string $reason = null, $cancelledBy = null): Order
    {
        return $this->orderRepository->transaction(function () use ($order, $reason, $cancelledBy) {
            try {
                // Update order status using repository
                $this->orderRepository->update($order, [
                    'status' => 'cancelled',
                    'cancel_reason' => $reason,
                ]);

                // Restore product quantities
                $this->inventoryService->restoreStock($order);

                // Send notification
                $this->notificationService->notifyUserOfCancellation($order, $reason);

                Log::info('Order cancelled', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'cancelled_by' => $cancelledBy ? get_class($cancelledBy) . ':' . $cancelledBy->id : 'system',
                    'reason' => $reason,
                ]);

                return $order->fresh();

            } catch (\Exception $e) {
                Log::error('Failed to cancel order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update delivery person's order status
     *
     * @param int $orderId
     * @param string $status
     * @return Order|null|false
     * @throws \Exception
     */
    public function updateDeliveryOrderStatus(int $orderId, string $status)
    {
        $user = auth()->user();

        if ($user->type !== 'delivery') {
            return null;
        }

        // Delivery users must act only on orders that are already 'new' (not 'pending')
        $order = Order::whereIn('status', ['new', 'delivered', 'confirmed'])
            ->where('delivery_id', $user->id)
            ->find($orderId);

            

        if (!$order) {
            return null;
        }

        // Delivery can only update to confirmed or delivered
        if (!in_array($status, ['delivered', 'confirmed'])) {
            return false;
        }

        // Validate status transition
        if (!$this->isValidTransition($order->status, $status)) {
            // Return false so controller can handle invalid transitions gracefully
            return false;
        }

        // Business rule: delivery persons must not mark an order as 'delivered'
        // unless the order is already in 'confirmed' status. Prevent skipping
        // 'confirmed' -> 'delivered' transition (e.g., from 'new' directly to 'delivered').
        if ($status === 'delivered' && $order->status !== 'confirmed') {
            return false;
        }

        // Update order status
        $this->orderRepository->update($order, ['status' => $status]);

        // Send notifications
        // 1. Notify client of status change
        $this->notificationService->notifyUserOfStatusChange($order->fresh('user'), $status);

        // 2. Notify admins of delivery status update
        $this->notificationService->notifyAdminsOfDeliveryStatusUpdate($order->fresh(), $status);

        // Handle refund orders: process payment when delivered
        if ($status === 'delivered' && $order->is_refund) {
            $this->processRefundPaymentOnDelivery($order);
        } else if ($status === 'delivered') {
            // Normal order payment confirmation
            $this->orderRepository->update($order, ['payment_status' => 'success']);
        }

        return $order->fresh();
    }

    /**
     * Process refund payment when delivery confirms pickup
     *
     * @param Order $order
     * @return void
     */
    private function processRefundPaymentOnDelivery(Order $order): void
    {
        try {
            // Process refund to wallet
            $this->paymentService->refundToWallet($order);

            // Create transaction record
            $transactionService = app(\App\Services\TransactionService::class);
            $transactionService->createRefundTransaction(
                $order->user_id,
                $order->refund_amount,
                $order
            );

            // Update order to refunded status
            $this->orderRepository->update($order, [
                'status' => 'refunded',
                'payment_status' => 'refunded',
            ]);

            Log::info('Refund payment processed after delivery confirmation', [
                'order_id' => $order->id,
                'refund_number' => $order->refund_number,
                'refund_amount' => $order->refund_amount,
                'user_id' => $order->user_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process refund payment on delivery', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle side effects when status changes
     *
     * @param Order $order
     * @param string $oldStatus
     * @param string $newStatus
     */
    private function handleStatusSideEffects(Order $order, string $oldStatus, string $newStatus): void
    {
        // Restore stock when order is cancelled
        if ($newStatus === 'cancelled') {
            $this->inventoryService->restoreStock($order);
        }

        // Additional side effects can be added here
        // For example: update delivery person availability, send SMS, etc.
    }
}

