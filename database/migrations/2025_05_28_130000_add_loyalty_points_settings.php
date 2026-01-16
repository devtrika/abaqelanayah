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
        // Add loyalty points settings
        $loyaltySettings = [
            [
                'key' => 'loyalty_points_earn_rate',
                'value' => '1', // 1 point per 1 SAR spent
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loyalty_points_redeem_rate', 
                'value' => '1', // 1 point = 1 SAR value
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loyalty_points_enabled',
                'value' => '1', // Enable loyalty points system
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loyalty_points_min_redeem',
                'value' => '10', // Minimum points to redeem
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'loyalty_points_max_redeem_percentage',
                'value' => '50', // Maximum 50% of order total can be paid with points
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($loyaltySettings as $setting) {
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
            'loyalty_points_earn_rate',
            'loyalty_points_redeem_rate', 
            'loyalty_points_enabled',
            'loyalty_points_min_redeem',
            'loyalty_points_max_redeem_percentage',
        ])->delete();
    }
};
