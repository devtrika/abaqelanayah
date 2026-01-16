<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User update requests for profile changes
        
        DB::table('user_updates')->insert([
            [
                'user_id' => 1,
                'field_name' => 'phone',
                'old_value' => '12345678',
                'new_value' => '87654321',
                'status' => 'pending',
                'admin_notes' => null,
                'processed_by' => null,
                'processed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'field_name' => 'email',
                'old_value' => 'old@example.com',
                'new_value' => 'new@example.com',
                'status' => 'approved',
                'admin_notes' => 'Email change approved',
                'processed_by' => 1,
                'processed_at' => now()->subDays(1),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 3,
                'field_name' => 'name',
                'old_value' => 'John Doe',
                'new_value' => 'John Smith',
                'status' => 'rejected',
                'admin_notes' => 'Name change requires additional documentation',
                'processed_by' => 1,
                'processed_at' => now()->subHours(6),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => 1,
                'field_name' => 'city_id',
                'old_value' => '1',
                'new_value' => '2',
                'status' => 'approved',
                'admin_notes' => 'Location change approved',
                'processed_by' => 1,
                'processed_at' => now()->subHours(2),
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(2),
            ],
        ]);

        $this->command->info('User updates seeded successfully!');
    }
}
