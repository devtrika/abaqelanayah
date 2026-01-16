<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class SeedPaymentMethods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:payment-methods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed payment methods table with predefined values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding payment methods...');

        $paymentMethods = [
            [
                'name' => [
                    'en' => 'Visa',
                    'ar' => 'فيزا'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Mada',
                    'ar' => 'مدى'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Apple Pay',
                    'ar' => 'آبل باي'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Google Pay',
                    'ar' => 'جوجل باي'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Tabby',
                    'ar' => 'تابي'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Tamara',
                    'ar' => 'تمارا'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
            [
                'name' => [
                    'en' => 'Cash on Delivery',
                    'ar' => 'الدفع عند الاستلام'
                ],
                'image' => 'storage/images/default.png',
                'is_active' => true
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
            $this->info("Created payment method: " . json_encode($method['name']));
        }

        $this->info('Payment methods seeded successfully!');
    }
}
