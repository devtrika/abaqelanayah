<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User tokens for authentication and verification
        
        DB::table('user_tokens')->insert([
            [
                'user_id' => 1,
                'token' => hash('sha256', 'user-token-1-' . time()),
                'type' => 'auth',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'token' => hash('sha256', 'user-token-2-' . time()),
                'type' => 'verification',
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'token' => hash('sha256', 'user-token-3-' . time()),
                'type' => 'password_reset',
                'expires_at' => now()->addHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'token' => hash('sha256', 'user-token-4-' . time()),
                'type' => 'refresh',
                'expires_at' => now()->addDays(60),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('User tokens seeded successfully!');
    }
}
