<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Service ratings (legacy table - will be migrated to polymorphic rates)
        
        DB::table('service_rates')->insert([
            [
                'user_id' => 1,
                'service_id' => 1,
                'rate' => 5,
                'body' => 'Excellent hair cut service! Very professional and the result was exactly what I wanted.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'user_id' => 2,
                'service_id' => 1,
                'rate' => 4,
                'body' => 'Good service overall, but the appointment was delayed by 20 minutes.',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'user_id' => 3,
                'service_id' => 2,
                'rate' => 5,
                'body' => 'Amazing hair coloring! The color turned out perfect and lasted long.',
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'user_id' => 1,
                'service_id' => 3,
                'rate' => 5,
                'body' => 'Best manicure I have ever had! Very detailed and beautiful nail art.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 4,
                'service_id' => 3,
                'rate' => 4,
                'body' => 'Good manicure service. Clean tools and nice environment.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'user_id' => 2,
                'service_id' => 4,
                'rate' => 5,
                'body' => 'Relaxing pedicure with excellent foot massage. Highly recommended!',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 5,
                'service_id' => 5,
                'rate' => 4,
                'body' => 'Great facial treatment. My skin feels so much better and looks brighter.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 3,
                'service_id' => 6,
                'rate' => 5,
                'body' => 'Incredible massage! Very relaxing and therapeutic. Will book again.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 1,
                'service_id' => 7,
                'rate' => 5,
                'body' => 'Perfect bridal makeup! I looked stunning on my wedding day. Thank you!',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => 4,
                'service_id' => 8,
                'rate' => 4,
                'body' => 'Beautiful party makeup. Got many compliments at the event.',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
        ]);

        $this->command->info('Service rates seeded successfully!');
    }
}
