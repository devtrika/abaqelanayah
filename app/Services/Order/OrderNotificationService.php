<?php

namespace App\Services\Order;

use App\Models\Admin;
use App\Models\Order;

/**
 * Handles all order-related notifications
 */
class OrderNotificationService
{
    /**
     * Send new order notification to all admins
     *
     * @param Order $order
     */
    public function notifyAdminsOfNewOrder(Order $order): void
    {
        $orderNum = $order->order_number ?? $order->id;
        $message = 'يوجد طلب حجز جديد برقم #' . $orderNum;

        $admins = Admin::all();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'طلب جديد',
                    'en' => 'New Order',
                ],
                'body' => [
                    'ar' => $message,
                    'en' => 'New order #' . $orderNum,
                ],
                'type' => 'new_order',
                'order_id' => $order->id,
            ]));
        }
    }

    /**
     * Send order status update notification to user
     *
     * @param Order $order
     * @param string $newStatus
     */
    public function notifyUserOfStatusChange(Order $order, string $newStatus): void
    {
        $user = $order->user;

        if (!$user) {
            return;
        }

        $statusTranslations = [
            'pending' => ['ar' => 'قيد الانتظار', 'en' => 'Pending'],
            'new' => ['ar' => 'جديد', 'en' => 'New'],
            'confirmed' => ['ar' => 'تم التأكيد', 'en' => 'Confirmed'],
            'delivered' => ['ar' => 'تم التوصيل', 'en' => 'Delivered'],
            'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
            'refunded' => ['ar' => 'تم الاسترجاع', 'en' => 'Refunded'],
            'problem' => ['ar' => 'به مشكلة', 'en' => 'Problem'],
        ];

        $statusText = $statusTranslations[$newStatus] ?? ['ar' => $newStatus, 'en' => $newStatus];

        $user->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'تحديث حالة الطلب',
                'en' => 'Order Status Update',
            ],
            'body' => [
                'ar' => 'تم تحديث حالة طلبك #' . $order->order_number . ' إلى: ' . $statusText['ar'],
                'en' => 'Your order #' . $order->order_number . ' status updated to: ' . $statusText['en'],
            ],
            'type' => 'order_status_update',
            'order_id' => $order->id,
            'order_type' => $this->resolveOrderType($order),
        ]));
    }

    /**
     * Send order cancellation notification to user
     *
     * @param Order $order
     * @param string|null $reason
     */
    public function notifyUserOfCancellation(Order $order, ?string $reason = null): void
    {
        $user = $order->user;

        if (!$user) {
            return;
        }

        $bodyAr = 'تم إلغاء طلبك #' . $order->order_number;
        $bodyEn = 'Your order #' . $order->order_number . ' has been cancelled';

        if ($reason) {
            $bodyAr .= '. السبب: ' . $reason;
            $bodyEn .= '. Reason: ' . $reason;
        }

        $user->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'إلغاء الطلب',
                'en' => 'Order Cancelled',
            ],
            'body' => [
                'ar' => $bodyAr,
                'en' => $bodyEn,
            ],
            'type' => 'order_cancelled',
            'order_id' => $order->id,
            'order_type' => $this->resolveOrderType($order),
        ]));
    }

    /**
     * Send refund notification to user
     *
     * @param Order $order
     * @param float $amount
     */
    public function notifyUserOfRefund(Order $order, float $amount): void
    {
        $user = $order->user;

        if (!$user) {
            return;
        }

        $user->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'استرجاع المبلغ',
                'en' => 'Refund Processed',
            ],
            'body' => [
                'ar' => 'تم استرجاع مبلغ ' . $amount . ' ريال لطلبك #' . $order->order_number,
                'en' => 'Refund of ' . $amount . ' SAR processed for order #' . $order->order_number,
            ],
            'type' => 'order_refunded',
            'order_id' => $order->id,
            'order_type' => 'refund',
        ]));
    }

    /**
     * Notify delivery person when assigned to an order
     *
     * @param Order $order
     */
    public function notifyDeliveryOfAssignment(Order $order): void
    {
        $deliveryUser = $order->delivery;

        if (!$deliveryUser) {
            return;
        }
        // Respect delivery users' notification preference
        if ($deliveryUser->type === 'delivery' && !$deliveryUser->is_notify) {
            return;
        }

        $deliveryUser->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'طلب توصيل جديد',
                'en' => 'New Delivery Order',
            ],
            'body' => [
                'ar' => 'تم تعيينك لتوصيل الطلب #' . $order->order_number,
                'en' => 'You have been assigned to deliver order #' . $order->order_number,
            ],
            'type' => 'admin_notify',
            'order_id' => $order->id,
            'order_type' => 'normal',
        ]));
    }

    /**
     * Notify delivery person when assigned to a refund order (pickup)
     */
    public function notifyDeliveryOfRefundAssignment(Order $order): void
    {
        $deliveryUser = $order->delivery;
        if (!$deliveryUser) {
            \Log::warning('Refund assignment notification skipped: no delivery user on order', [
                'order_id' => $order->id,
                'refund_number' => $order->refund_number,
            ]);
            return;
        }
        // Respect delivery users' notification preference
        if ($deliveryUser->type === 'delivery' && !$deliveryUser->is_notify) {
            \Log::info('Delivery user has notifications disabled. Skipping refund assignment push.', [
                'order_id' => $order->id,
                'delivery_id' => $deliveryUser->id,
            ]);
            return;
        }

        $addressText = '';
        if ($order->address) {
            $parts = [];
            if (!empty($order->address->address_name)) { $parts[] = $order->address->address_name; }
            if (!empty($order->address->description)) { $parts[] = $order->address->description; }
            $addressText = implode(' - ', $parts);
        }

        // Debug info to help trace delivery device/token state
        try {
            $devicesCount = method_exists($deliveryUser, 'devices') ? (int) $deliveryUser->devices()->count() : null;
            \Log::info('Sending refund assignment notification to delivery', [
                'order_id' => $order->id,
                'refund_number' => $order->refund_number,
                'delivery_id' => $deliveryUser->id,
                'delivery_is_notify' => (bool) $deliveryUser->is_notify,
                'delivery_devices_count' => $devicesCount,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Unable to count delivery devices for notification', [
                'order_id' => $order->id,
                'delivery_id' => $deliveryUser->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        $deliveryUser->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'تعيين طلب استرجاع',
                'en' => 'Refund Assignment',
            ],
            'body' => [
                'ar' => 'تم تعيينك لاسترجاع الطلب #' . ($order->refund_number ?: $order->order_number) . ($addressText ? ' - العنوان: ' . $addressText : ''),
                'en' => 'You have been assigned to a refund order #' . ($order->refund_number ?: $order->order_number) . ($addressText ? ' - Address: ' . $addressText : ''),
            ],
            // Keep type stable; client apps may rely on it. Provide an explicit event hint too.
            'type' => 'refund_assignment',
            'order_type' => 'refund',
            'event' => 'delivery_assigned_to_refund',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'refund_number' => $order->refund_number,
        ]));
    }

    /**
     * Notify client when delivery person is assigned to their order
     *
     * @param Order $order
     */
    public function notifyClientOfDeliveryAssignment(Order $order): void
    {
        $user = $order->user;

        if (!$user) {
            return;
        }

        $user->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'تعيين مندوب التوصيل',
                'en' => 'Delivery Person Assigned',
            ],
            'body' => [
                'ar' => 'تم تعيين مندوب توصيل لطلبك #' . $order->order_number,
                'en' => 'A delivery person has been assigned to your order #' . $order->order_number,
            ],
            'type' => 'admin_notify',
            'order_id' => $order->id,
            'order_type' => $this->resolveOrderType($order),
        ]));
    }

    /**
     * Notify admins when delivery person updates order status
     *
     * @param Order $order
     * @param string $newStatus
     */
    public function notifyAdminsOfDeliveryStatusUpdate(Order $order, string $newStatus): void
    {
        $statusTranslations = [
            'confirmed' => ['ar' => 'تم التأكيد', 'en' => 'Confirmed'],
            'delivered' => ['ar' => 'تم التوصيل', 'en' => 'Delivered'],
        ];

        $statusText = $statusTranslations[$newStatus] ?? ['ar' => $newStatus, 'en' => $newStatus];

        $admins = Admin::all();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'تحديث حالة الطلب من المندوب',
                    'en' => 'Order Status Updated by Delivery',
                ],
                'body' => [
                    'ar' => 'قام مندوب التوصيل بتحديث حالة الطلب #' . $order->order_number . ' إلى: ' . $statusText['ar'],
                    'en' => 'Delivery person updated order #' . $order->order_number . ' status to: ' . $statusText['en'],
                ],
                'type' => 'delivery_status_update',
                'order_id' => $order->id,
            ]));
        }
    }

    /**
     * Notify delivery person when added to a branch
     *
     * @param \App\Models\User $deliveryUser
     * @param \App\Models\Branch $branch
     */
    public function notifyDeliveryOfBranchAssignment($deliveryUser, $branch): void
    {
        if (!$deliveryUser || $deliveryUser->type !== 'delivery') {
            return;
        }
        // Respect delivery users' notification preference
        if (!$deliveryUser->is_notify) {
            return;
        }

        $deliveryUser->notify(new \App\Notifications\NotifyUser([
            'title' => [
                'ar' => 'تعيين في فرع',
                'en' => 'Branch Assignment',
            ],
            'body' => [
                'ar' => 'تم تعيينك في فرع: ' . $branch->name,
                'en' => 'You have been assigned to branch: ' . $branch->name,
            ],
            'type' => 'branch_assignment',
            'branch_id' => $branch->id,
        ]));
    }

    /**
     * Determine order type for notifications based on refund state.
     * Returns 'refund' when the order is a refund flow (is_refund=true, refundable=true, or has refund_number).
     */
    private function resolveOrderType(Order $order): string
    {
        try {
            if (method_exists($order, 'isRefund') && $order->isRefund()) {
                return 'refund';
            }
        } catch (\Throwable $e) {
            // Ignore and fallback to attributes
        }

        if (!empty($order->refund_number) || (bool)($order->refundable ?? false) || (bool)($order->is_refund ?? false)) {
            return 'refund';
        }

        return 'normal';
    }

}

