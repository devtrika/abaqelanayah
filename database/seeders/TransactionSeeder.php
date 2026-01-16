<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Financial transactions for users
        
        DB::table('transactions')->insert([
            [
                'user_id' => 1,
                'type' => 'payment',
                'amount' => 50.00,
                'description' => 'Payment for Order #1 - Hair Cut & Style',
                'reference' => 'PAY_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 2,
                'type' => 'payment',
                'amount' => 33.00,
                'description' => 'Payment for Order #2 - Manicure & Pedicure',
                'reference' => 'PAY_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'knet',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 3,
                'type' => 'payment',
                'amount' => 85.00,
                'description' => 'Payment for Order #3 - Facial & Massage',
                'reference' => 'PAY_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'wallet',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 1,
                'type' => 'refund',
                'amount' => 25.00,
                'description' => 'Refund for cancelled service booking',
                'reference' => 'REF_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 5,
                'type' => 'payment',
                'amount' => 128.00,
                'description' => 'Payment for Order #5 - Bridal Makeup Package',
                'reference' => 'PAY_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'bank_transfer',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => 2,
                'type' => 'wallet_topup',
                'amount' => 100.00,
                'description' => 'Wallet top-up via credit card',
                'reference' => 'TOP_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ],
            [
                'user_id' => 4,
                'type' => 'payment',
                'amount' => 27.50,
                'description' => 'Payment for Order #4 - Hair Cut',
                'reference' => 'PAY_' . \Illuminate\Support\Str::random(10),
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => 3,
                'type' => 'loyalty_redemption',
                'amount' => 10.00,
                'description' => 'Loyalty points redeemed for discount',
                'reference' => 'LOY_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'loyalty_points',
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ],
            [
                'user_id' => 1,
                'type' => 'payment',
                'amount' => 60.00,
                'description' => 'Payment for Course Enrollment - Hair Styling Basics',
                'reference' => 'CRS_' . \Illuminate\Support\Str::random(10),
                'status' => 'completed',
                'payment_method' => 'knet',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => 2,
                'type' => 'refund',
                'amount' => 60.00,
                'description' => 'Refund for cancelled order #6',
                'reference' => 'REF_' . \Illuminate\Support\Str::random(10),
                'status' => 'processing',
                'payment_method' => 'knet',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
        ]);

        $this->command->info('Transactions seeded successfully!');
    }
}
