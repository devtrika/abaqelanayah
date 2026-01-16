<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Chat rooms between users and providers
        
        DB::table('rooms')->insert([
            [
                'name' => 'User 1 - Provider 1',
                'type' => 'private',
                'description' => 'Chat room for service booking discussion',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subHours(2),
            ],
            [
                'name' => 'User 2 - Provider 2',
                'type' => 'private',
                'description' => 'Chat room for nail service consultation',
                'is_active' => true,
                'created_by' => 2,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subHours(5),
            ],
            [
                'name' => 'User 3 - Provider 3',
                'type' => 'private',
                'description' => 'Chat room for skincare consultation',
                'is_active' => true,
                'created_by' => 3,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subHours(1),
            ],
            [
                'name' => 'User 1 - Provider 4',
                'type' => 'private',
                'description' => 'Chat room for makeup service booking',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subHours(8),
            ],
            [
                'name' => 'User 4 - Provider 1',
                'type' => 'private',
                'description' => 'Chat room for hair service inquiry',
                'is_active' => false,
                'created_by' => 4,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            [
                'name' => 'Support Group',
                'type' => 'group',
                'description' => 'General support and announcements',
                'is_active' => true,
                'created_by' => 1, // Admin
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subHours(12),
            ],
        ]);

        $this->command->info('Rooms seeded successfully!');
    }
}
