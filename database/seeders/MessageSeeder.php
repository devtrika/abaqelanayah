<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Messages in chat rooms
        
        DB::table('messages')->insert([
            // Room 1: User 1 - Provider 1
            [
                'room_id' => 1,
                'user_id' => 1,
                'message' => 'Hi! I would like to book a hair cut appointment.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(9),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(9),
            ],
            [
                'room_id' => 1,
                'user_id' => 11, // Provider 1
                'message' => 'Hello! I would be happy to help you. What time works best for you?',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(9),
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'room_id' => 1,
                'user_id' => 1,
                'message' => 'How about tomorrow at 2 PM?',
                'message_type' => 'text',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            
            // Room 2: User 2 - Provider 2
            [
                'room_id' => 2,
                'user_id' => 2,
                'message' => 'I need a manicure and pedicure for this weekend.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(7),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(7),
            ],
            [
                'room_id' => 2,
                'user_id' => 12, // Provider 2
                'message' => 'Perfect! I have availability on Saturday. Would you prefer home service or salon?',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(7),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'room_id' => 2,
                'user_id' => 2,
                'message' => 'Home service would be great! Here is my address.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subHours(6),
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(6),
            ],
            
            // Room 3: User 3 - Provider 3
            [
                'room_id' => 3,
                'user_id' => 3,
                'message' => 'I have sensitive skin. What facial treatment do you recommend?',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(5),
            ],
            [
                'room_id' => 3,
                'user_id' => 13, // Provider 3
                'message' => 'For sensitive skin, I recommend our gentle hydrating facial. It uses natural ingredients.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'room_id' => 3,
                'user_id' => 3,
                'message' => 'That sounds perfect! Can I book for next week?',
                'message_type' => 'text',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            
            // Room 4: User 1 - Provider 4
            [
                'room_id' => 4,
                'user_id' => 1,
                'message' => 'I need bridal makeup for my wedding next month.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(3),
            ],
            [
                'room_id' => 4,
                'user_id' => 14, // Provider 4
                'message' => 'Congratulations! I would love to help with your special day. Let me send you my portfolio.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'room_id' => 4,
                'user_id' => 14, // Provider 4
                'message' => 'portfolio.jpg',
                'message_type' => 'image',
                'is_read' => true,
                'read_at' => now()->subHours(8),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subHours(8),
            ],
            
            // Room 6: Support Group
            [
                'room_id' => 6,
                'user_id' => 1, // Admin
                'message' => 'Welcome to our support group! Feel free to ask any questions.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(29),
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(29),
            ],
            [
                'room_id' => 6,
                'user_id' => 2,
                'message' => 'Thank you! This platform is amazing.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(24),
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(24),
            ],
            [
                'room_id' => 6,
                'user_id' => 1, // Admin
                'message' => 'We have added new features this week. Check them out!',
                'message_type' => 'text',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
        ]);

        $this->command->info('Messages seeded successfully!');
    }
}
