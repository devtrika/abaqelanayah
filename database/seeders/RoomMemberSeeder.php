<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Room members for chat rooms
        
        DB::table('room_members')->insert([
            // Room 1: User 1 - Provider 1
            [
                'room_id' => 1,
                'user_id' => 1,
                'role' => 'member',
                'joined_at' => now()->subDays(10),
                'last_seen_at' => now()->subHours(2),
                'is_active' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subHours(2),
            ],
            [
                'room_id' => 1,
                'user_id' => 11, // Provider 1 user ID
                'role' => 'member',
                'joined_at' => now()->subDays(10),
                'last_seen_at' => now()->subHours(3),
                'is_active' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subHours(3),
            ],
            
            // Room 2: User 2 - Provider 2
            [
                'room_id' => 2,
                'user_id' => 2,
                'role' => 'member',
                'joined_at' => now()->subDays(8),
                'last_seen_at' => now()->subHours(5),
                'is_active' => true,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subHours(5),
            ],
            [
                'room_id' => 2,
                'user_id' => 12, // Provider 2 user ID
                'role' => 'member',
                'joined_at' => now()->subDays(8),
                'last_seen_at' => now()->subHours(6),
                'is_active' => true,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subHours(6),
            ],
            
            // Room 3: User 3 - Provider 3
            [
                'room_id' => 3,
                'user_id' => 3,
                'role' => 'member',
                'joined_at' => now()->subDays(6),
                'last_seen_at' => now()->subHours(1),
                'is_active' => true,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subHours(1),
            ],
            [
                'room_id' => 3,
                'user_id' => 13, // Provider 3 user ID
                'role' => 'member',
                'joined_at' => now()->subDays(6),
                'last_seen_at' => now()->subHours(2),
                'is_active' => true,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subHours(2),
            ],
            
            // Room 4: User 1 - Provider 4
            [
                'room_id' => 4,
                'user_id' => 1,
                'role' => 'member',
                'joined_at' => now()->subDays(4),
                'last_seen_at' => now()->subHours(8),
                'is_active' => true,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subHours(8),
            ],
            [
                'room_id' => 4,
                'user_id' => 14, // Provider 4 user ID
                'role' => 'member',
                'joined_at' => now()->subDays(4),
                'last_seen_at' => now()->subHours(9),
                'is_active' => true,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subHours(9),
            ],
            
            // Room 5: User 4 - Provider 1 (inactive)
            [
                'room_id' => 5,
                'user_id' => 4,
                'role' => 'member',
                'joined_at' => now()->subDays(2),
                'last_seen_at' => now()->subDays(1),
                'is_active' => false,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            [
                'room_id' => 5,
                'user_id' => 11, // Provider 1 user ID
                'role' => 'member',
                'joined_at' => now()->subDays(2),
                'last_seen_at' => now()->subDays(1),
                'is_active' => false,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            
            // Room 6: Support Group (multiple members)
            [
                'room_id' => 6,
                'user_id' => 1,
                'role' => 'admin',
                'joined_at' => now()->subDays(30),
                'last_seen_at' => now()->subHours(12),
                'is_active' => true,
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subHours(12),
            ],
            [
                'room_id' => 6,
                'user_id' => 2,
                'role' => 'member',
                'joined_at' => now()->subDays(25),
                'last_seen_at' => now()->subHours(15),
                'is_active' => true,
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subHours(15),
            ],
            [
                'room_id' => 6,
                'user_id' => 3,
                'role' => 'member',
                'joined_at' => now()->subDays(20),
                'last_seen_at' => now()->subHours(18),
                'is_active' => true,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subHours(18),
            ],
        ]);

        $this->command->info('Room members seeded successfully!');
    }
}
