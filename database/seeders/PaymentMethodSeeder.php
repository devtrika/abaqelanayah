<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'id' => 1,
                'name' => [
                    'en' => 'Visa',
                    'ar' => 'فيزا'
                ],
                'image' => 'storage/images/paymentmethods/visa.png',
                'is_active' => true
            ],
            [
                'id' => 2,
                'name' => [
                    'en' => 'Mada',
                    'ar' => 'مدى'
                ],
                'image' => 'storage/images/paymentmethods/mada.png',
                'is_active' => true
            ],
            [
                'id' => 3,
                'name' => [
                    'en' => 'Apple Pay',
                    'ar' => 'آبل باي'
                ],
                'image' => 'storage/images/paymentmethods/applepay.png',
                'is_active' => true
            ],
            [
                'id' => 4,
                'name' => [
                    'en' => 'Google Pay',
                    'ar' => 'جوجل باي'
                ],
                'image' => 'storage/images/paymentmethods/googlepay.png',
                'is_active' => true
            ],
            [
                'id' => 5,
                'name' => [
                    'en' => 'Cash On Delivry',
                    'ar' => 'الدفع عند الاستلام'
                ],
                'image' => 'storage/images/paymentmethods/wallet.png',
                'is_active' => true
            ],
            [
                'id' => 6,
                'name' => [
                    'en' => 'Wallet',
                    'ar' => 'المحفظه'
                ],
                'image' => 'storage/images/paymentmethods/wallet.png',
                'is_active' => true
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['id' => $method['id']], // Search criteria
                $method // Data to update or create
            );
        }
    }
}
