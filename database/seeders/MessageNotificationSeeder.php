<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Message notifications for users
        
        DB::table('message_notifications')->insert([
            [
                'message_id' => 1,
                'user_id' => 11, // Provider 1 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(9),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(9),
            ],
            [
                'message_id' => 2,
                'user_id' => 1, // User 1 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(9),
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'message_id' => 3,
                'user_id' => 11, // Provider 1 receiving notification
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'message_id' => 4,
                'user_id' => 12, // Provider 2 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(7),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(7),
            ],
            [
                'message_id' => 5,
                'user_id' => 2, // User 2 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(7),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'message_id' => 6,
                'user_id' => 12, // Provider 2 receiving notification
                'is_read' => true,
                'read_at' => now()->subHours(6),
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(6),
            ],
            [
                'message_id' => 7,
                'user_id' => 13, // Provider 3 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(5),
            ],
            [
                'message_id' => 8,
                'user_id' => 3, // User 3 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'message_id' => 9,
                'user_id' => 13, // Provider 3 receiving notification
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'message_id' => 10,
                'user_id' => 14, // Provider 4 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(3),
            ],
            [
                'message_id' => 11,
                'user_id' => 1, // User 1 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'message_id' => 12,
                'user_id' => 1, // User 1 receiving notification
                'is_read' => true,
                'read_at' => now()->subHours(8),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subHours(8),
            ],
            [
                'message_id' => 13,
                'user_id' => 2, // User 2 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(29),
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(29),
            ],
            [
                'message_id' => 13,
                'user_id' => 3, // User 3 receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(29),
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(29),
            ],
            [
                'message_id' => 14,
                'user_id' => 1, // Admin receiving notification
                'is_read' => true,
                'read_at' => now()->subDays(24),
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(24),
            ],
            [
                'message_id' => 15,
                'user_id' => 2, // User 2 receiving notification
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'message_id' => 15,
                'user_id' => 3, // User 3 receiving notification
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
        ]);

        $this->command->info('Message notifications seeded successfully!');
    }
}
