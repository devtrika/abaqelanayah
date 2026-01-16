<?php

namespace App\Notifications;

class OrderStatusNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct($order = null, $newStatus = null, array $data = [], array $channels = ['database', 'firebase'])
    {
        // Support both old and new constructor signatures
        if ($order && $newStatus) {
            $data = array_merge([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $newStatus,
                'customer_name' => $order->user->name ?? '',
            ], $data);
        }

        parent::__construct($data, $channels);
    }

    /**
     * Get notification type identifier.
     */
    protected function getNotificationType(): string
    {
        return 'order_status';
    }

    /**
     * Get notification title.
     */
    protected function getTitle($notifiable): string
    {
        $status = $this->data['status'] ?? 'updated';
        $orderNumber = $this->data['order_number'] ?? '';

        $titleKey = "notifications.order_status.{$status}.title";

        return $this->trans($titleKey, [
            'order_number' => $orderNumber,
        ]);
    }

    /**
     * Get notification body.
     */
    protected function getBody($notifiable): string
    {
        $status = $this->data['status'] ?? 'updated';
        $orderNumber = $this->data['order_number'] ?? '';
        $customerName = $notifiable->name ?? '';

        $bodyKey = "notifications.order_status.{$status}.body";

        return $this->trans($bodyKey, [
            'customer_name' => $customerName,
            'order_number' => $orderNumber,
            'delivery_time' => $this->data['delivery_time'] ?? '',
            'delivery_person' => $this->data['delivery_person'] ?? '',
        ]);
    }

    /**
     * Get action URL.
     */
    protected function getActionUrl($notifiable): ?string
    {
        $orderId = $this->data['order_id'] ?? null;

        if ($orderId) {
            return route('client.orders.show', $orderId);
        }

        return parent::getActionUrl($notifiable);
    }

    /**
     * Get notification icon.
     */
    protected function getIcon($notifiable): string
    {
        $status = $this->data['status'] ?? 'updated';

        $icons = [
            'confirmed' => '✅',
            'preparing' => '👨‍🍳',
            'ready' => '📦',
            'out_for_delivery' => '🚚',
            'delivered' => '✅',
            'cancelled' => '❌',
        ];

        return $icons[$status] ?? '📋';
    }

    /**
     * Get Android notification channel ID.
     */
    protected function getAndroidChannelId(): string
    {
        return 'order_updates';
    }

    /**
     * Get notification priority based on status.
     */
    protected function getPriority(): string
    {
        $status = $this->data['status'] ?? 'updated';

        $highPriorityStatuses = ['confirmed', 'out_for_delivery', 'delivered', 'cancelled'];

        return in_array($status, $highPriorityStatuses) ? 'high' : 'normal';
    }

    /**
     * Get notification sound based on status.
     */
    protected function getSound(): string
    {
        $status = $this->data['status'] ?? 'updated';

        $sounds = [
            'confirmed' => 'order_confirmed.mp3',
            'delivered' => 'order_delivered.mp3',
            'cancelled' => 'order_cancelled.mp3',
        ];

        return $sounds[$status] ?? 'default';
    }
}
