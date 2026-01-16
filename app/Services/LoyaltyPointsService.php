<?php

namespace App\Services;

use App\Models\User;
use App\Models\SiteSetting;

class LoyaltyPointsService
{
    public function __construct(protected TransactionService $transactionService )
    {
    }
    /**
     * Process loyalty points for a completed order
     * 
     * @param User $user
     * @param float $totalAmount - Total order amount
     * @param float $pointsUsed - Points used in this order (in points, not SAR)
     * @param string $paymentMethod - Payment method used
     * @return array
     */
    public function processOrderLoyaltyPoints(User $user, $totalAmount, $pointsUsed = 0, $paymentMethod = 'electronic' , $order)
    {
        $appInfo = SiteSetting::pluck('value', 'key')->toArray();
        $settings = SettingService::appInformations($appInfo);
        
        // Check if loyalty points are enabled
        if (!$settings['loyalty_points_enabled']) {
            return [
                'points_earned' => 0,
                'points_used' => 0,
                'points_value_used' => 0,
            ];
        }

        $pointsEarned = 0;
        $pointsValueUsed = 0;

        // Deduct used points from user balance
        if ($pointsUsed > 0) {
            $user->useLoyaltyPoints($pointsUsed);
            $redeemRate = $settings['loyalty_points_redeem_rate'] ?? 1;
            $pointsValueUsed = $pointsUsed * $redeemRate;
        }

        // Calculate points earned only on actual payment amount
        // Points are not earned on the portion paid with loyalty points
        $actualPaymentAmount = $totalAmount - $pointsValueUsed;
        
        if ($actualPaymentAmount > 0) {
            $pointsEarned = $user->calculateLoyaltyPointsEarned($actualPaymentAmount);
            
            if ($pointsEarned > 0) {
                $user->addLoyaltyPoints($pointsEarned);
            }
        }
        $this->transactionService->createLoyaltyPointsRewardTransaction($user->id, $pointsEarned, $order->order_number);

        return [
            'points_earned' => $pointsEarned,
            'points_used' => $pointsUsed,
            'points_value_used' => $pointsValueUsed,
            'actual_payment_amount' => $actualPaymentAmount,
        ];
    }

    /**
     * Get loyalty points settings
     * 
     * @return array
     */
    public function getSettings()
    {
        $appInfo = SiteSetting::pluck('value', 'key')->toArray();
        return SettingService::appInformations($appInfo);
    }

    /**
     * Validate loyalty points usage for a cart
     * 
     * @param User $user
     * @param int $pointsToUse
     * @param float $cartTotal
     * @return array
     */
    public function validatePointsUsage(User $user, $pointsToUse, $cartTotal)
    {
        $settings = $this->getSettings();
        
        if (!$settings['loyalty_points_enabled']) {
            return [
                'valid' => false,
                'message' => 'Loyalty points system is disabled'
            ];
        }

        // Check minimum points requirement
        $minRedeem = $settings['loyalty_points_min_redeem'] ?? 10;
        if ($pointsToUse < $minRedeem) {
            return [
                'valid' => false,
                'message' => "Minimum {$minRedeem} points required to redeem"
            ];
        }

        // Check if user has enough points
        if ($user->loyalty_points < $pointsToUse) {
            return [
                'valid' => false,
                'message' => 'Insufficient loyalty points'
            ];
        }

        // Check maximum percentage limit
        $maxPercentage = $settings['loyalty_points_max_redeem_percentage'] ?? 50;
        $redeemRate = $settings['loyalty_points_redeem_rate'] ?? 1;
        
        $pointsValue = $pointsToUse * $redeemRate;
        $maxAllowedValue = ($cartTotal * $maxPercentage) / 100;
        
        if ($pointsValue > $maxAllowedValue) {
            $maxPoints = floor($maxAllowedValue / $redeemRate);
            return [
                'valid' => false,
                'message' => "Cannot use more than {$maxPoints} points ({$maxPercentage}% of cart total)"
            ];
        }

        return [
            'valid' => true,
            'points_value' => $pointsValue,
            'message' => 'Valid points usage'
        ];
    }

    /**
     * Get user loyalty points summary
     * 
     * @param User $user
     * @return array
     */
    public function getUserPointsSummary(User $user)
    {
        $settings = $this->getSettings();
        
        return [
            'enabled' => $settings['loyalty_points_enabled'] ?? false,
            'balance' => $user->loyalty_points ?? 0,
            'value_in_sar' => $user->loyalty_points_value ?? 0,
            'earn_rate' => $settings['loyalty_points_earn_rate'] ?? 1,
            'redeem_rate' => $settings['loyalty_points_redeem_rate'] ?? 1,
            'min_redeem' => $settings['loyalty_points_min_redeem'] ?? 10,
            'max_redeem_percentage' => $settings['loyalty_points_max_redeem_percentage'] ?? 50,
        ];
    }
}
