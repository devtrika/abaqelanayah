<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Log activities for system monitoring
        
        DB::table('log_activities')->insert([
            [
                'subject' => 'User Login',
                'url' => '/api/auth/login',
                'method' => 'POST',
                'ip' => '192.168.1.100',
                'agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X)',
                'user_id' => 1,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'subject' => 'Service Booking',
                'url' => '/api/bookings',
                'method' => 'POST',
                'ip' => '192.168.1.101',
                'agent' => 'Mozilla/5.0 (Android 12; Mobile)',
                'user_id' => 2,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'subject' => 'Product Purchase',
                'url' => '/api/orders',
                'method' => 'POST',
                'ip' => '192.168.1.102',
                'agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'user_id' => 3,
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'subject' => 'Profile Update',
                'url' => '/api/profile',
                'method' => 'PUT',
                'ip' => '192.168.1.103',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'user_id' => 1,
                'created_at' => now()->subMinutes(15),
                'updated_at' => now()->subMinutes(15),
            ],
            [
                'subject' => 'Admin Login',
                'url' => '/admin/login',
                'method' => 'POST',
                'ip' => '192.168.1.1',
                'agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'user_id' => null,
                'created_at' => now()->subMinutes(5),
                'updated_at' => now()->subMinutes(5),
            ],
        ]);

        $this->command->info('Log activities seeded successfully!');
    }
}
