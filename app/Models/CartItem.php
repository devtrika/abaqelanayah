<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'weight_option_id',
        'cutting_option_id',
        'packaging_option_id',
        'price',
        'discount_amount',
        'total',
    ];
    public function weightOption(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'weight_option_id');
    }

    public function cuttingOption(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'cutting_option_id');
    }

    public function packagingOption(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'packaging_option_id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Alias for compatibility: treat item() as product() for now
     */
    public function item(): BelongsTo
    {
        return $this->product();
    }


}
