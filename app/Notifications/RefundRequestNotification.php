<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RefundRequestNotification extends Notification
{
    use Queueable;

    private $MessageData;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $orderNum = $order->order_number ?? $order->id;
        $userName = $order->user?->name ?? 'Unknown';

        // Get refund items count
        $refundItemsCount = $order->items()->where('request_refund', true)->count();

        // Get refund reason if exists
        $refundReason = $order->refundReason?->reason ?? 'بدون سبب محدد';

        // Build detailed body messages in Arabic and English
        $bodyAr = "طلب استرجاع جديد من {$userName} للطلب #{$orderNum}";
        $bodyEn = "New refund request from {$userName} for order #{$orderNum}";

        $this->MessageData = [
            'title' => 'طلب استرجاع جديد',
            'body' => [
                'ar' => $bodyAr,
                'en' => $bodyEn,
            ],
            'type' => 'refund_request',
            'order_id' => $order->id,
            'user_name' => $userName,
            'order_number' => $orderNum,
            'items_count' => $refundItemsCount,
            'refund_reason' => $refundReason,
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->MessageData;
    }
}

