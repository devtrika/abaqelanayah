<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Traits\Firebase;

class OrderStatusUpdateNotification extends Notification
{
    use Queueable, Firebase;

    protected $data;

    public function __construct($order)
    {
        $this->data = [
            'order_id'   => $order->id,
            'order_num'  => $order->order_num,
            'date'       => $order->updated_at->format('Y-m-d H:i:s'),
            'message'    => 'تم تحديث حالة طلبك رقم ' . $order->order_num . ' إلى ' . __('order.' . $order->status),
            'type'       => 'order_status_update',
        ];
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $tokens = [];
        $types  = [];

        if (count($notifiable->devices)) {
            foreach ($notifiable->devices as $device) {
                $tokens[] = $device->device_id;
                $types[]  = $device->device_type;
            }
            $this->sendFcmNotification($tokens, $types, $this->data, $notifiable->lang);
        }

        return $this->data;
    }
}
