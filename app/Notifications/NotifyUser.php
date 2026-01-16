<?php

namespace App\Notifications;

use App\Traits\Firebase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyUser extends Notification
{
    use Queueable;
    use Firebase;
    private $MessageData;
   
    public function __construct($request)
    {
        // Handle both localized and simple string formats
        if (isset($request['title_ar']) || isset($request['title_en'])) {
            // Localized format from admin form
            $title = [
                'ar' => $request['title_ar'] ?? '',
                'en' => $request['title_en'] ?? ''
            ];
            $body = [
                'ar' => $request['body_ar'] ?? '',
                'en' => $request['body_en'] ?? ''
            ];
        } else {
            // Simple string format (backward compatibility)
            $title = $request['title'] ?? '';
            $body = $request['body'] ?? '';
        }

        // Merge any extra fields passed (e.g., order_id, type, etc.) into the payload
        $extra = is_array($request) ? $request : [];
        unset($extra['title'], $extra['body'], $extra['title_ar'], $extra['title_en'], $extra['body_ar'], $extra['body_en']);

        $this->MessageData = array_merge([
            'title' => $title,
            'body' => $body,
            'type' => $request['type'] ?? 'admin_notify',
        ], $extra);
    }

    public function via($notifiable)
    {
        // Respect delivery users' notification preference
        if ($notifiable instanceof \App\Models\User && $notifiable->type === 'delivery' && !$notifiable->is_notify) {
            return [];
        }
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        // Respect delivery users' notification preference for push
        if ($notifiable instanceof \App\Models\User && $notifiable->type === 'delivery' && !$notifiable->is_notify) {
            \Log::info('Delivery user disabled notifications, skipping FCM send', [
                'user_id' => $notifiable->id,
            ]);
            return $this->MessageData;
        }

        $tokens = [];
        $types  = [];

        if(count($notifiable->devices)){
            foreach ($notifiable->devices as $device) {
                $tokens[] = $device->device_id ;
                $types[]  = $device->device_type ;
            }

            // Send FCM notification and handle response
            $result = $this->sendFcmNotification($tokens, $types, $this->MessageData, $notifiable->lang ?? 'ar');

            if (!$result['success']) {
                \Log::error('Failed to send FCM notification for admin notify', [
                    'user_id' => $notifiable->id,
                    'error' => $result['error'] ?? 'Unknown error',
                    'tokens_count' => count($tokens),
                    'message_data' => $this->MessageData
                ]);
            } else {
                \Log::info('FCM notification sent successfully for admin notify', [
                    'user_id' => $notifiable->id,
                    'success_count' => $result['success_count'] ?? 0,
                    'tokens_count' => count($tokens),
                    'message_data' => $this->MessageData
                ]);
            }
        } else {
            \Log::warning('No devices found for user notification', [
                'user_id' => $notifiable->id,
                'message_data' => $this->MessageData
            ]);
        }

        return $this->MessageData ;
    }
}
