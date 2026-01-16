<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderRatedNotification extends Notification
{
    use Queueable;

    protected $orderId;
    protected $userId;

    public function __construct($orderId, $userId)
    {
        $this->orderId = $orderId;
        $this->userId = $userId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'تقييم جديد',
            'body' => "تم تقييم الطلب رقم #{$this->orderId} من المستخدم رقم #{$this->userId}",
            'order_id' => $this->orderId,
            'user_id' => $this->userId,
        ];
    }
}
