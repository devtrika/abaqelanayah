<?php

namespace App\Notifications;

class SystemNotification extends BaseNotification
{
    /**
     * Get notification type identifier.
     */
    protected function getNotificationType(): string
    {
        return 'system';
    }

    /**
     * Get notification title.
     */
    protected function getTitle($notifiable): string
    {
        return $this->data['title'] ?? $this->trans('notifications.system.default.title');
    }

    /**
     * Get notification body.
     */
    protected function getBody($notifiable): string
    {
        $customerName = $notifiable->name ?? '';
        
        return $this->data['body'] ?? $this->trans('notifications.system.default.body', [
            'customer_name' => $customerName,
        ]);
    }

    /**
     * Get notification icon.
     */
    protected function getIcon($notifiable): string
    {
        $type = $this->data['system_type'] ?? 'info';
        
        $icons = [
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'success' => '✅',
            'error' => '❌',
            'maintenance' => '🔧',
            'update' => '🔄',
            'promotion' => '🎉',
        ];

        return $icons[$type] ?? 'ℹ️';
    }

    /**
     * Get Android notification channel ID.
     */
    protected function getAndroidChannelId(): string
    {
        $type = $this->data['system_type'] ?? 'info';
        
        $channels = [
            'maintenance' => 'system_maintenance',
            'promotion' => 'promotions',
            'update' => 'app_updates',
        ];

        return $channels[$type] ?? 'system_general';
    }

    /**
     * Get notification priority based on system type.
     */
    protected function getPriority(): string
    {
        $type = $this->data['system_type'] ?? 'info';
        
        $highPriorityTypes = ['maintenance', 'error', 'update'];
        $urgentTypes = ['maintenance'];
        
        if (in_array($type, $urgentTypes)) {
            return 'urgent';
        }
        
        return in_array($type, $highPriorityTypes) ? 'high' : 'normal';
    }

    /**
     * Get notification sound based on system type.
     */
    protected function getSound(): string
    {
        $type = $this->data['system_type'] ?? 'info';
        
        $sounds = [
            'maintenance' => 'maintenance_alert.mp3',
            'promotion' => 'promotion.mp3',
            'error' => 'error_alert.mp3',
        ];

        return $sounds[$type] ?? 'default';
    }
}
