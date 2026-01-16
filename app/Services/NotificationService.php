<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use App\Notifications\Channels\FirebaseChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Exception;

class NotificationService
{
    /**
     * Available notification channels.
     */
    const CHANNEL_DATABASE = 'database';
    const CHANNEL_FIREBASE = 'firebase';
    const CHANNEL_MAIL = 'mail';
    const CHANNEL_SMS = 'sms';

    /**
     * Default channels to use.
     */
    protected array $defaultChannels = [
        self::CHANNEL_DATABASE,
        self::CHANNEL_FIREBASE,
    ];

    /**
     * Send notification to a user or users.
     */
    public function send($users, string $notificationType, array $data = [], array $channels = null): array
    {
        try {
            // Normalize users to collection
            $users = $this->normalizeUsers($users);
            
            if ($users->isEmpty()) {
                throw new Exception('No users provided for notification');
            }

            // Use default channels if none specified
            $channels = $channels ?? $this->defaultChannels;

            // Create notification instance
            $notification = $this->createNotification($notificationType, $data, $channels);

            if (!$notification) {
                throw new Exception("Notification type '{$notificationType}' not found");
            }

            // Send notification
            $results = [];
            foreach ($users as $user) {
                try {
                    // Respect delivery users' notification preference
                    if ($user instanceof \App\Models\User && $user->type === 'delivery' && !$user->is_notify) {
                        $results[] = [
                            'user_id' => $user->id,
                            'status' => 'skipped',
                            'reason' => 'notifications_disabled',
                        ];
                        continue;
                    }

                    $user->notify($notification);
                    $results[] = [
                        'user_id' => $user->id,
                        'status' => 'success',
                        'channels' => $channels,
                    ];
                } catch (Exception $e) {
                    Log::error('Failed to send notification to user', [
                        'user_id' => $user->id,
                        'notification_type' => $notificationType,
                        'error' => $e->getMessage(),
                    ]);

                    $results[] = [
                        'user_id' => $user->id,
                        'status' => 'failed',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;

        } catch (Exception $e) {
            Log::error('Notification service error', [
                'notification_type' => $notificationType,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send notification to specific user by ID.
     */
    public function sendToUser(int $userId, string $notificationType, array $data = [], array $channels = null): array
    {
        $user = User::find($userId);
        
        if (!$user) {
            throw new Exception("User with ID {$userId} not found");
        }

        return $this->send($user, $notificationType, $data, $channels);
    }

    /**
     * Send notification to multiple users by IDs.
     */
    public function sendToUsers(array $userIds, string $notificationType, array $data = [], array $channels = null): array
    {
        $users = User::whereIn('id', $userIds)->get();
        return $this->send($users, $notificationType, $data, $channels);
    }

    /**
     * Broadcast notification to all users.
     */
    public function broadcast(string $notificationType, array $data = [], array $channels = null): array
    {
        // For performance, we'll chunk users and process in batches
        $results = [];
        
        User::chunk(100, function ($users) use ($notificationType, $data, $channels, &$results) {
            $batchResults = $this->send($users, $notificationType, $data, $channels);
            $results = array_merge($results, $batchResults);
        });

        return $results;
    }

    /**
     * Queue notification for later sending.
     */
    public function queue($users, string $notificationType, array $data = [], array $channels = null, $delay = null): void
    {
        $users = $this->normalizeUsers($users);
        $channels = $channels ?? $this->defaultChannels;
        
        $notification = $this->createNotification($notificationType, $data, $channels);
        
        if (!$notification) {
            throw new Exception("Notification type '{$notificationType}' not found");
        }

        foreach ($users as $user) {
            // Respect delivery users' notification preference
            if ($user instanceof \App\Models\User && $user->type === 'delivery' && !$user->is_notify) {
                continue;
            }

            if ($delay) {
                $user->notifyAt($delay, $notification);
            } else {
                $user->notify($notification->onQueue('notifications'));
            }
        }
    }

    /**
     * Register device token for user.
     */
    public function registerDeviceToken(int $userId, string $deviceToken, array $attributes = []): UserDeviceToken
    {
        return UserDeviceToken::createOrUpdate($userId, $deviceToken, $attributes);
    }

    /**
     * Remove device token.
     */
    public function removeDeviceToken(string $deviceToken): bool
    {
        return UserDeviceToken::where('device_token', $deviceToken)->delete() > 0;
    }

    /**
     * Get notification history for user.
     */
    public function getNotificationHistory(int $userId, int $limit = 50): Collection
    {
        $user = User::find($userId);
        
        if (!$user) {
            return collect();
        }

        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $notificationId): bool
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllAsRead(int $userId): int
    {
        $user = User::find($userId);
        
        if (!$user) {
            return 0;
        }

        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Normalize users input to collection.
     */
    protected function normalizeUsers($users): Collection
    {
        if ($users instanceof User) {
            return collect([$users]);
        }

        if ($users instanceof Collection) {
            return $users;
        }

        if (is_array($users)) {
            return collect($users);
        }

        if (is_numeric($users)) {
            $user = User::find($users);
            return $user ? collect([$user]) : collect();
        }

        return collect();
    }

    /**
     * Create notification instance based on type.
     */
    protected function createNotification(string $type, array $data, array $channels): ?Notification
    {
        $notificationClass = $this->getNotificationClass($type);
        
        if (!$notificationClass || !class_exists($notificationClass)) {
            return null;
        }

        return new $notificationClass($data, $channels);
    }

    /**
     * Get notification class name from type.
     */
    protected function getNotificationClass(string $type): string
    {
        $className = str_replace('_', '', ucwords($type, '_'));
        return "App\\Notifications\\{$className}Notification";
    }

    /**
     * Get available notification channels.
     */
    public function getAvailableChannels(): array
    {
        return [
            self::CHANNEL_DATABASE,
            self::CHANNEL_FIREBASE,
            self::CHANNEL_MAIL,
            self::CHANNEL_SMS,
        ];
    }

    /**
     * Set default channels.
     */
    public function setDefaultChannels(array $channels): self
    {
        $this->defaultChannels = $channels;
        return $this;
    }
}
