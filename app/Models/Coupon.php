<?php

namespace App\Models;

use Carbon\Carbon;

class Coupon extends BaseModel
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'coupon_name',
        'coupon_num',
        'provider_id',
        'type',
        'discount',
        'max_discount',
        'start_date',
        'expire_date',
        'is_active',
        'usage_time',
        'max_use',
        'used_times'
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'expire_date' => 'datetime',
        'is_active' => 'boolean',
        'max_use' => 'integer',
        'used_times' => 'integer'
    ];

    /**
     * Get the provider that owns the coupon
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the gifts that belong to the coupon.
     */
    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }

    /**
     * Get orders that used this coupon
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }

    /**
     * Check if coupon is currently valid
     */
    public function getIsValidAttribute()
    {
        $now = Carbon::now();

        return $this->is_active &&
               ($this->start_date === null || $now->gte($this->start_date)) &&
               ($this->expire_date === null || $now->lte($this->expire_date));
    }

    /**
     * Get remaining uses for the coupon
     */
    public function getRemainingUsesAttribute()
    {
        if ($this->max_use === 0) {
            return 'unlimited';
        }

        return max(0, $this->max_use - $this->used_times);
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute()
    {
        if ($this->max_use === 0) {
            return 0;
        }

        return round(($this->used_times / $this->max_use) * 100, 2);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid coupons (active and within date range)
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('expire_date')
                          ->orWhere('expire_date', '>=', $now);
                    });
                  
    }

    /**
     * Scope for provider-specific coupons
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    

    // public function getUsageTimeAttribute(){
    //     return Order::where('coupon_id' , $this->id)->count();
    // }


}
