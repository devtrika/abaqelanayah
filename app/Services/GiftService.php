<?php

namespace App\Services;

use App\Models\Gift;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GiftService
{
    /**
     * Get active gifts for the current month
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveGiftsForCurrentMonth()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        
        return Gift::where('is_active', true)
            ->whereYear('month', Carbon::now()->year)
            ->whereMonth('month', Carbon::now()->month)
            ->with(['coupon'])
            ->get();
    }

    /**
     * Get user's order count for the current month
     *
     * @param int $userId
     * @return int
     */
    public function getUserOrderCountForCurrentMonth($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return 0;
        }

        return Order::where('user_id', $userId)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'delivered') // Exclude cancelled orders
            ->count();
    }

    /**
     * Calculate progress percentage for a gift
     *
     * @param Gift $gift
     * @param int $userOrderCount
     * @return float
     */
    public function calculateProgress(Gift $gift, $userOrderCount)
    {
        if ($gift->orders_count <= 0) {
            return 0;
        }

        $progress = ($userOrderCount / $gift->orders_count) * 100;
        
        // Cap at 100%
        return min($progress, 100);
    }

    /**
     * Check if user has achieved the gift requirements
     *
     * @param Gift $gift
     * @param int $userOrderCount
     * @return bool
     */
    public function hasUserAchievedGift(Gift $gift, $userOrderCount)
    {
        return $userOrderCount >= $gift->orders_count;
    }

    /**
     * Get gifts with user progress data
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGiftsWithProgress($userId = null)
    {
        $gifts = $this->getActiveGiftsForCurrentMonth();
        $userOrderCount = $this->getUserOrderCountForCurrentMonth($userId);

        return $gifts->map(function ($gift) use ($userOrderCount) {
            $gift->user_order_count = $userOrderCount;
            $gift->progress = $this->calculateProgress($gift, $userOrderCount);
            $gift->is_achieved = $this->hasUserAchievedGift($gift, $userOrderCount);
            
            return $gift;
        });
    }

    /**
     * Get available gifts that user can see coupon for
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableGifts($userId = null)
    {
        return $this->getGiftsWithProgress($userId)->filter(function ($gift) {
            return $gift->is_achieved;
        });
    }

    /**
     * Get pending gifts that user is working towards
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingGifts($userId = null)
    {
        return $this->getGiftsWithProgress($userId)->filter(function ($gift) {
            return !$gift->is_achieved;
        });
    }
}
