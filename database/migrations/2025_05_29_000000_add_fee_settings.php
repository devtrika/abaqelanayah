<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fee settings to site_settings table
        $feeSettings = [
            [
                'key' => 'booking_fee_amount',
                'value' => '10.00', // Fixed booking fee for services
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_home_service_fee',
                'value' => '15.00', // Default home service fee (if provider doesn't set one)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'normal_delivery_fee',
                'value' => '5.00', // Normal delivery fee for products
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'express_delivery_fee',
                'value' => '15.00', // Express delivery fee for products
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cancellation_fee_amount',
                'value' => '5.00', // Fixed cancellation fee
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'cancellation_fee_percentage',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'product_commission',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'service_commission',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'product_referral_commission',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'service_referral_commission',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'key' => 'comission_withdrawal_fee',
                'value' => '0', // Percentage-based cancellation fee (0 = use fixed amount)
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($feeSettings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        SiteSetting::whereIn('key', [
            'booking_fee_amount',
            'default_home_service_fee',
            'normal_delivery_fee',
            'express_delivery_fee',
            'cancellation_fee_amount',
            'cancellation_fee_percentage',
        ])->delete();
    }
};
