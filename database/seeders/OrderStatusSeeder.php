<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Order status history tracking

        DB::table('order_statuses')->insert([
            // Order 1 status history
            [
                'order_id' => 1,
                'provider_sub_order_id' => 1,
                'status' => 'pending_payment',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 1,
                'map_desc' => 'Order placed successfully',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'order_id' => 1,
                'provider_sub_order_id' => 1,
                'status' => 'processing',
                'statusable_type' => 'App\\Models\\Admin',
                'statusable_id' => 1,
                'map_desc' => 'Payment confirmed, processing order',
                'created_at' => now()->subDays(4)->addHours(2),
                'updated_at' => now()->subDays(4)->addHours(2),
            ],
            [
                'order_id' => 1,
                'provider_sub_order_id' => 1,
                'status' => 'confirmed',
                'statusable_type' => 'App\\Models\\Provider',
                'statusable_id' => 1,
                'map_desc' => 'Order confirmed by provider',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'order_id' => 1,
                'provider_sub_order_id' => 1,
                'status' => 'completed',
                'statusable_type' => 'App\\Models\\Provider',
                'statusable_id' => 1,
                'map_desc' => 'Service completed successfully',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],

            // Order 2 status history
            [
                'order_id' => 2,
                'provider_sub_order_id' => 2,
                'status' => 'pending_payment',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 2,
                'map_desc' => 'Order placed successfully',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'order_id' => 2,
                'provider_sub_order_id' => 2,
                'status' => 'processing',
                'statusable_type' => 'App\\Models\\Admin',
                'statusable_id' => 1,
                'map_desc' => 'Payment confirmed, processing order',
                'created_at' => now()->subDays(2)->addHours(4),
                'updated_at' => now()->subDays(2)->addHours(4),
            ],
            [
                'order_id' => 2,
                'provider_sub_order_id' => 2,
                'status' => 'confirmed',
                'statusable_type' => 'App\\Models\\Provider',
                'statusable_id' => 2,
                'map_desc' => 'Order confirmed by provider',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],

            // Order 3 status history
            [
                'order_id' => 3,
                'provider_sub_order_id' => 3,
                'status' => 'pending_payment',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 3,
                'map_desc' => 'Order placed successfully',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'order_id' => 3,
                'provider_sub_order_id' => 3,
                'status' => 'processing',
                'statusable_type' => 'App\\Models\\Admin',
                'statusable_id' => 1,
                'map_desc' => 'Payment confirmed, processing order',
                'created_at' => now()->subDays(1)->addHours(6),
                'updated_at' => now()->subDays(1)->addHours(6),
            ],
            [
                'order_id' => 3,
                'provider_sub_order_id' => 3,
                'status' => 'confirmed',
                'statusable_type' => 'App\\Models\\Provider',
                'statusable_id' => 3,
                'map_desc' => 'Order confirmed by provider',
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ],

            // Order 4 status history
            [
                'order_id' => 4,
                'provider_sub_order_id' => 4,
                'status' => 'pending_payment',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 4,
                'map_desc' => 'Order placed, awaiting payment',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],

            // Order 5 status history (cancelled)
            [
                'order_id' => 5,
                'provider_sub_order_id' => 5,
                'status' => 'pending_payment',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 5,
                'map_desc' => 'Order placed successfully',
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ],
            [
                'order_id' => 5,
                'provider_sub_order_id' => 5,
                'status' => 'cancelled',
                'statusable_type' => 'App\\Models\\User',
                'statusable_id' => 5,
                'map_desc' => 'Order cancelled due to payment failure',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
        ]);

        $this->command->info('Order statuses seeded successfully!');
    }
}
