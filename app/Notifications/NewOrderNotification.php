<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Traits\Firebase;

class NewOrderNotification extends Notification
{
    use Queueable, Firebase;

    protected $data;

    public function __construct($order)
    {
        $this->data = [
            'order_id'   => $order->id,
            'order_num'  => $order->order_num,
            'date'       => $order->created_at->format('Y-m-d H:i:s'),
            'message'    => 'لديك طلب جديد برقم ' . $order->order_num,
            'type'       => 'new_order',
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
            
            // Send FCM notification and handle response
            $result = $this->sendFcmNotification($tokens, $types, $this->data, $notifiable->lang ?? 'ar');
            
            if (!$result['success']) {
                \Log::error('Failed to send FCM notification for new order', [
                    'order_id' => $this->data['order_id'],
                    'user_id' => $notifiable->id,
                    'error' => $result['error'] ?? 'Unknown error',
                    'tokens_count' => count($tokens)
                ]);
            } else {
                \Log::info('FCM notification sent successfully for new order', [
                    'order_id' => $this->data['order_id'],
                    'user_id' => $notifiable->id,
                    'success_count' => $result['success_count'] ?? 0,
                    'tokens_count' => count($tokens)
                ]);
            }
        } else {
            \Log::warning('No devices found for user notification', [
                'order_id' => $this->data['order_id'],
                'user_id' => $notifiable->id
            ]);
        }

        return $this->data;
    }
}
