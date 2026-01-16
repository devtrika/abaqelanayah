<?php

namespace App\Notifications;

use App\Notifications\Channels\FirebaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;
    protected array $channels;
    protected ?array $firebaseResponse = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data = [], array $channels = ['database', 'firebase'])
    {
        $this->data = $data;
        $this->channels = $channels;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Respect delivery users' notification preference
        if ($notifiable instanceof \App\Models\User && $notifiable->type === 'delivery' && !$notifiable->is_notify) {
            return [];
        }
        $channels = [];

        foreach ($this->channels as $channel) {
            switch ($channel) {
                case 'database':
                    $channels[] = 'database';
                    break;
                case 'firebase':
                    $channels[] = FirebaseChannel::class;
                    break;
                case 'mail':
                    if ($notifiable->email) {
                        $channels[] = 'mail';
                    }
                    break;
                case 'sms':
                    // Add SMS channel if implemented
                    break;
            }
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->getNotificationType(),
            'title' => $this->getTitle($notifiable),
            'body' => $this->getBody($notifiable),
            'data' => $this->getData($notifiable),
            'action_url' => $this->getActionUrl($notifiable),
            'icon' => $this->getIcon($notifiable),
            'priority' => $this->getPriority(),
            'channels_sent' => $this->channels,
            'firebase_response' => $this->firebaseResponse,
        ];
    }

    /**
     * Get the Firebase representation of the notification.
     */
    public function toFirebase($notifiable): array
    {
        return [
            'title' => $this->getTitle($notifiable),
            'body' => $this->getBody($notifiable),
            'icon' => $this->getIcon($notifiable),
            'click_action' => $this->getActionUrl($notifiable),
            'sound' => $this->getSound(),
            'priority' => $this->getFirebasePriority(),
            'data' => array_merge($this->getData($notifiable), [
                'notification_type' => $this->getNotificationType(),
                'created_at' => now()->toISOString(),
            ]),
            'android' => $this->getAndroidConfig($notifiable),
            'apns' => $this->getApnsConfig($notifiable),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getTitle($notifiable))
            ->line($this->getBody($notifiable));

        $actionUrl = $this->getActionUrl($notifiable);
        if ($actionUrl) {
            $mail->action($this->getActionText($notifiable), $actionUrl);
        }

        return $mail;
    }

    /**
     * Set Firebase response data.
     */
    public function setFirebaseResponse(array $response): void
    {
        $this->firebaseResponse = $response;
    }

    /**
     * Get notification type identifier.
     */
    abstract protected function getNotificationType(): string;

    /**
     * Get notification title.
     */
    abstract protected function getTitle($notifiable): string;

    /**
     * Get notification body.
     */
    abstract protected function getBody($notifiable): string;

    /**
     * Get notification data.
     */
    protected function getData($notifiable): array
    {
        return $this->data;
    }

    /**
     * Get action URL.
     */
    protected function getActionUrl($notifiable): ?string
    {
        return $this->data['action_url'] ?? null;
    }

    /**
     * Get action text for email.
     */
    protected function getActionText($notifiable): string
    {
        return $this->data['action_text'] ?? __('notifications.view_details');
    }

    /**
     * Get notification icon.
     */
    protected function getIcon($notifiable): string
    {
        return $this->data['icon'] ?? config('app.url') . '/favicon.ico';
    }

    /**
     * Get notification priority.
     */
    protected function getPriority(): string
    {
        return $this->data['priority'] ?? 'normal';
    }

    /**
     * Get Firebase priority.
     */
    protected function getFirebasePriority(): string
    {
        $priority = $this->getPriority();
        return in_array($priority, ['high', 'urgent']) ? 'high' : 'normal';
    }

    /**
     * Get notification sound.
     */
    protected function getSound(): string
    {
        return $this->data['sound'] ?? 'default';
    }

    /**
     * Get Android specific configuration.
     */
    protected function getAndroidConfig($notifiable): array
    {
        return [
            'priority' => $this->getFirebasePriority(),
            'notification' => [
                'channel_id' => $this->getAndroidChannelId(),
                'sound' => $this->getSound(),
                'color' => $this->getAndroidColor(),
            ],
        ];
    }

    /**
     * Get APNS (iOS) specific configuration.
     */
    protected function getApnsConfig($notifiable): array
    {
        return [
            'payload' => [
                'aps' => [
                    'sound' => $this->getSound(),
                    'badge' => $this->getBadgeCount($notifiable),
                    'content-available' => 1,
                ],
            ],
        ];
    }

    /**
     * Get Android notification channel ID.
     */
    protected function getAndroidChannelId(): string
    {
        return $this->data['android_channel_id'] ?? 'default';
    }

    /**
     * Get Android notification color.
     */
    protected function getAndroidColor(): string
    {
        return $this->data['android_color'] ?? '#2196F3';
    }

    /**
     * Get badge count for iOS.
     */
    protected function getBadgeCount($notifiable): int
    {
        return $notifiable->unreadNotifications()->count() + 1;
    }

    /**
     * Get localized string.
     */
    protected function trans(string $key, array $replace = []): string
    {
        $locale = $this->data['locale'] ?? App::getLocale();
        return __($key, $replace, $locale);
    }
}
