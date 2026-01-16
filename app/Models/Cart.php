<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'total',
        'coupon_code',
        'vat_amount',
        'wallet_deduction',
        'coupon_value',
        'loyalty_points_used',
        'loyalty_points_value',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the coupon applied to the cart (maps coupon_code -> coupon_num)
     */
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class, 'coupon_code', 'coupon_num');
    }
}
