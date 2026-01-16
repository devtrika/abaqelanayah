<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalAccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Personal access tokens are typically created dynamically
        // This seeder creates some sample tokens for testing
        
        DB::table('personal_access_tokens')->insert([
            [
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 1,
                'name' => 'mobile-app',
                'token' => hash('sha256', 'sample-token-1'),
                'abilities' => json_encode(['*']),
                'last_used_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2,
                'name' => 'web-app',
                'token' => hash('sha256', 'sample-token-2'),
                'abilities' => json_encode(['read', 'write']),
                'last_used_at' => now()->subHours(2),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(2),
            ],
            [
                'tokenable_type' => 'App\\Models\\Admin',
                'tokenable_id' => 1,
                'name' => 'admin-panel',
                'token' => hash('sha256', 'admin-token-1'),
                'abilities' => json_encode(['*']),
                'last_used_at' => now()->subMinutes(30),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subMinutes(30),
            ],
        ]);

        $this->command->info('Personal access tokens seeded successfully!');
    }
}
