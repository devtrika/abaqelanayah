<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'weight',
        'cutting',
        'packaging',
        'weight_option_id',
        'cutting_option_id',
        'packaging_option_id',
        'price',
        'cutting_price',
        'packaging_price',
        'discount_amount',
        'total',
        // Refund fields
        'is_refunded',
        'refund_quantity',
        'refund_amount',
        'request_refund',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
        'options' => 'array',
        // Refund casts
        'is_refunded' => 'boolean',
        'refund_quantity' => 'integer',
        'refund_amount' => 'decimal:2',
        'request_refund' => 'boolean',
    ];

    /**
     * Get the order that owns the order item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the owning item model (Product or Service)
     */
    public function item()
    {
        return $this->morphTo();
    }

    /**
     * Get the product if item is a product
     */

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function weightOption()
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'weight_option_id');
    }

    public function cuttingOption()
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'cutting_option_id');
    }

    public function packagingOption()
    {
        return $this->belongsTo(\App\Models\ProductOption::class, 'packaging_option_id');
    }

    /**
     * Check if item is a product
     */
    public function isProduct()
    {
        return $this->item_type === 'App\Models\Product';
    }

    /**
     * Check if item is a service
     */
    public function isService()
    {
        return $this->item_type === 'App\Models\Service';
    }

    /**
     * Get the provider of the item
     */
    public function getProviderAttribute()
    {
        if ($this->isProduct()) {
            return $this->item->provider;
        } elseif ($this->isService()) {
            return $this->item->provider;
        }
        return null;
    }

    /**
     * Get item image (for products)
     */
    public function getItemImageAttribute()
    {
        if ($this->isProduct() && $this->item) {
            return $this->item->product_images_urls->first() ?? null;
        }
        return null;
    }
}
