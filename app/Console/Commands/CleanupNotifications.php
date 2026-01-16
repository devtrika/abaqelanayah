<?php

namespace App\Console\Commands;

use App\Models\UserDeviceToken;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:cleanup 
                            {--read-days=30 : Days to keep read notifications}
                            {--unread-days=90 : Days to keep unread notifications}
                            {--token-days=30 : Days to keep inactive device tokens}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old notifications and inactive device tokens';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('notifications.cleanup.enabled', true)) {
            $this->info('Notification cleanup is disabled in configuration.');
            return self::SUCCESS;
        }

        $readDays = $this->option('read-days') ?: config('notifications.cleanup.read_notifications_days', 30);
        $unreadDays = $this->option('unread-days') ?: config('notifications.cleanup.unread_notifications_days', 90);
        $tokenDays = $this->option('token-days') ?: config('notifications.cleanup.inactive_device_tokens_days', 30);
        $dryRun = $this->option('dry-run');

        $this->info('Starting notification cleanup...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be deleted');
        }

        // Clean up read notifications
        $readNotificationsCount = $this->cleanupReadNotifications($readDays, $dryRun);
        
        // Clean up old unread notifications
        $unreadNotificationsCount = $this->cleanupUnreadNotifications($unreadDays, $dryRun);
        
        // Clean up inactive device tokens
        $tokensCount = $this->cleanupInactiveTokens($tokenDays, $dryRun);

        $this->info('Cleanup completed:');
        $this->line("- Read notifications: {$readNotificationsCount}");
        $this->line("- Unread notifications: {$unreadNotificationsCount}");
        $this->line("- Inactive device tokens: {$tokensCount}");

        Log::info('Notification cleanup completed', [
            'read_notifications_deleted' => $readNotificationsCount,
            'unread_notifications_deleted' => $unreadNotificationsCount,
            'device_tokens_deleted' => $tokensCount,
            'dry_run' => $dryRun,
        ]);

        return self::SUCCESS;
    }

    /**
     * Clean up read notifications older than specified days.
     */
    protected function cleanupReadNotifications(int $days, bool $dryRun): int
    {
        $query = DatabaseNotification::whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays($days));

        $count = $query->count();

        if (!$dryRun && $count > 0) {
            $query->delete();
        }

        $this->line("Read notifications older than {$days} days: {$count}");

        return $count;
    }

    /**
     * Clean up unread notifications older than specified days.
     */
    protected function cleanupUnreadNotifications(int $days, bool $dryRun): int
    {
        $query = DatabaseNotification::whereNull('read_at')
            ->where('created_at', '<', now()->subDays($days));

        $count = $query->count();

        if (!$dryRun && $count > 0) {
            $query->delete();
        }

        $this->line("Unread notifications older than {$days} days: {$count}");

        return $count;
    }

    /**
     * Clean up inactive device tokens.
     */
    protected function cleanupInactiveTokens(int $days, bool $dryRun): int
    {
        $count = UserDeviceToken::cleanupInactiveTokens($days);

        if ($dryRun) {
            // For dry run, just count without deleting
            $count = UserDeviceToken::where('is_active', false)
                ->where('updated_at', '<', now()->subDays($days))
                ->count();
        }

        $this->line("Inactive device tokens older than {$days} days: {$count}");

        return $count;
    }
}
