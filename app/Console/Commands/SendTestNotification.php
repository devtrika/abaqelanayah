<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Exception;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:test 
                            {user? : User ID to send test notification to}
                            {--type=system : Notification type (order_status, refund_status, system)}
                            {--channels=* : Channels to use (database, firebase, mail)}
                            {--broadcast : Send to all users}';

    /**
     * The console command description.
     */
    protected $description = 'Send a test notification to verify the notification system';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        try {
            $userId = $this->argument('user');
            $type = $this->option('type');
            $channels = $this->option('channels');
            $broadcast = $this->option('broadcast');

            // Use default channels if none specified
            if (empty($channels)) {
                $channels = ['database', 'firebase'];
            }

            $this->info("Sending test notification...");
            $this->line("Type: {$type}");
            $this->line("Channels: " . implode(', ', $channels));

            if ($broadcast) {
                $this->warn("Broadcasting to ALL users...");
                if (!$this->confirm('Are you sure you want to send to all users?')) {
                    $this->info('Cancelled.');
                    return self::SUCCESS;
                }

                $results = $notificationService->broadcast($type, $this->getTestData($type), $channels);
                $this->displayResults($results);
                
            } elseif ($userId) {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found.");
                    return self::FAILURE;
                }

                $this->line("Recipient: {$user->name} ({$user->email})");
                
                $results = $notificationService->sendToUser($userId, $type, $this->getTestData($type), $channels);
                $this->displayResults($results);
                
            } else {
                // Interactive mode - let user select
                $users = User::limit(10)->get();
                
                if ($users->isEmpty()) {
                    $this->error('No users found in the system.');
                    return self::FAILURE;
                }

                $this->info('Available users:');
                foreach ($users as $user) {
                    $this->line("{$user->id}: {$user->name} ({$user->email})");
                }

                $selectedUserId = $this->ask('Enter user ID to send test notification to');
                
                if (!$selectedUserId || !is_numeric($selectedUserId)) {
                    $this->error('Invalid user ID.');
                    return self::FAILURE;
                }

                $results = $notificationService->sendToUser($selectedUserId, $type, $this->getTestData($type), $channels);
                $this->displayResults($results);
            }

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error("Failed to send test notification: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Get test data for different notification types.
     */
    protected function getTestData(string $type): array
    {
        switch ($type) {
            case 'order_status':
                return [
                    'order_id' => 12345,
                    'order_number' => 'ORD-TEST-001',
                    'status' => 'confirmed',
                    'delivery_time' => '30-45 minutes',
                ];

            case 'refund_status':
                return [
                    'refund_id' => 67890,
                    'refund_number' => 'REF-TEST-001',
                    'status' => 'approved',
                    'amount' => '150.00 SAR',
                    'reason' => 'Product quality issue',
                ];

            case 'system':
                return [
                    'title' => 'Test System Notification',
                    'body' => 'This is a test notification to verify the notification system is working correctly.',
                    'system_type' => 'info',
                    'action_url' => route('home'),
                    'action_text' => 'View Details',
                ];

            default:
                return [
                    'title' => 'Test Notification',
                    'body' => 'This is a test notification.',
                ];
        }
    }

    /**
     * Display notification results.
     */
    protected function displayResults(array $results): void
    {
        $this->info("\nNotification Results:");
        
        $successful = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['status'] === 'success') {
                $successful++;
                $this->line("✅ User {$result['user_id']}: Success");
            } else {
                $failed++;
                $this->line("❌ User {$result['user_id']}: Failed - " . ($result['error'] ?? 'Unknown error'));
            }
        }

        $this->info("\nSummary:");
        $this->line("Successful: {$successful}");
        $this->line("Failed: {$failed}");
        $this->line("Total: " . count($results));
    }
}
