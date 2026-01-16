<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;

/**
 * CouponRepository
 * 
 * Handles all database operations for coupons
 */
class CouponRepository
{
    /**
     * Find coupon by code
     *
     * @param string $code
     * @return Coupon|null
     */
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::where('coupon_num', $code)->first();
    }

    /**
     * Find coupon by code or fail
     *
     * @param string $code
     * @return Coupon
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByCodeOrFail(string $code): Coupon
    {
        return Coupon::where('coupon_num', $code)->firstOrFail();
    }

    /**
     * Find coupon by ID
     *
     * @param int $id
     * @return Coupon|null
     */
    public function find(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    /**
     * Update coupon
     *
     * @param Coupon $coupon
     * @param array $data
     * @return bool
     */
    public function update(Coupon $coupon, array $data): bool
    {
        return $coupon->update($data);
    }

    /**
     * Increment coupon usage count
     *
     * @param Coupon $coupon
     * @param int $count
     * @return int
     */
    public function incrementUsage(Coupon $coupon, int $count = 1): int
    {
        return $coupon->increment('used_times', $count);
    }

    /**
     * Decrement coupon usage time
     *
     * @param Coupon $coupon
     * @param int $count
     * @return int
     */
    public function decrementUsageTime(Coupon $coupon, int $count = 1): int
    {
        return $coupon->decrement('usage_time', $count);
    }

    /**
     * Check if coupon is active
     *
     * @param Coupon $coupon
     * @return bool
     */
    public function isActive(Coupon $coupon): bool
    {
        return isset($coupon->is_active) && $coupon->is_active == 1;
    }

    /**
     * Check if coupon is valid (not expired, not closed, has usage left)
     *
     * @param Coupon $coupon
     * @return bool
     */
    public function isValid(Coupon $coupon): bool
    {
        // Check if active
        if (!$this->isActive($coupon)) {
            return false;
        }

        // Check status
        if (in_array($coupon->status, ['closed', 'usage_end', 'expire'])) {
            return false;
        }

        // Check expiry date
        if ($coupon->expire_date < today()) {
            return false;
        }

        // Check start date
        if ($coupon->start_date > today()) {
            return false;
        }

        // Check usage time
        if ($coupon->usage_time <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Get all active coupons
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return Coupon::where('is_active', 1)
            ->where('status', '!=', 'closed')
            ->where('expire_date', '>=', today())
            ->where('start_date', '<=', today())
            ->where('usage_time', '>', 0)
            ->get();
    }

    /**
     * Get coupon by ID with validation
     *
     * @param int $id
     * @return Coupon|null
     */
    public function findValid(int $id): ?Coupon
    {
        $coupon = $this->find($id);
        
        if ($coupon && $this->isValid($coupon)) {
            return $coupon;
        }

        return null;
    }

    /**
     * Calculate coupon discount value
     *
     * @param Coupon $coupon
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'ratio') {
            $discount = ($subtotal * $coupon->discount) / 100;

            // Apply max discount limit if set
            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } elseif ($coupon->type === 'number') {
            $discount = $coupon->discount;
        } else {
            $discount = 0;
        }

        return round($discount, 2);
    }

    /**
     * Check if user can use coupon
     *
     * @param Coupon $coupon
     * @param int $userId
     * @return bool
     */
    public function canUserUseCoupon(Coupon $coupon, int $userId): bool
    {
        // Add any user-specific validation here
        // For example: check if user has already used this coupon
        // This depends on your business rules
        
        return $this->isValid($coupon);
    }
}

