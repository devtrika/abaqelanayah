<?php

namespace App\Notifications;

class RefundStatusNotification extends BaseNotification
{
    /**
     * Get notification type identifier.
     */
    protected function getNotificationType(): string
    {
        return 'refund_status';
    }

    /**
     * Get notification title.
     */
    protected function getTitle($notifiable): string
    {
        $status = $this->data['status'] ?? 'updated';
        $refundNumber = $this->data['refund_number'] ?? '';

        $titleKey = "notifications.refund_status.{$status}.title";
        
        return $this->trans($titleKey, [
            'refund_number' => $refundNumber,
        ]);
    }

    /**
     * Get notification body.
     */
    protected function getBody($notifiable): string
    {
        $status = $this->data['status'] ?? 'updated';
        $refundNumber = $this->data['refund_number'] ?? '';
        $customerName = $notifiable->name ?? '';
        $amount = $this->data['amount'] ?? '';

        $bodyKey = "notifications.refund_status.{$status}.body";
        
        return $this->trans($bodyKey, [
            'customer_name' => $customerName,
            'refund_number' => $refundNumber,
            'amount' => $amount,
            'reason' => $this->data['reason'] ?? '',
        ]);
    }

    /**
     * Get action URL.
     */
    protected function getActionUrl($notifiable): ?string
    {
        $refundId = $this->data['refund_id'] ?? null;
        
        if ($refundId) {
            return route('client.refunds.show', $refundId);
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
            'pending' => '⏳',
            'approved' => '✅',
            'rejected' => '❌',
            'processed' => '💰',
        ];

        return $icons[$status] ?? '💸';
    }

    /**
     * Get Android notification channel ID.
     */
    protected function getAndroidChannelId(): string
    {
        return 'refund_updates';
    }

    /**
     * Get notification priority based on status.
     */
    protected function getPriority(): string
    {
        $status = $this->data['status'] ?? 'updated';
        
        $highPriorityStatuses = ['approved', 'rejected', 'processed'];
        
        return in_array($status, $highPriorityStatuses) ? 'high' : 'normal';
    }

    /**
     * Get notification sound based on status.
     */
    protected function getSound(): string
    {
        $status = $this->data['status'] ?? 'updated';
        
        $sounds = [
            'approved' => 'refund_approved.mp3',
            'rejected' => 'refund_rejected.mp3',
            'processed' => 'refund_processed.mp3',
        ];

        return $sounds[$status] ?? 'default';
    }
}
