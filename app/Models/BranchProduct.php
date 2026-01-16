<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchProduct extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    /**
     * Get the branch that owns the branch product.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the product that owns the branch product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter by available quantity (qty > 0)
     */
    public function scopeAvailable($query)
    {
        return $query->where('qty', '>', 0);
    }

    /**
     * Check if the product is available in sufficient quantity
     */
    public function hasStock($requiredQty = 1)
    {
        return $this->qty >= $requiredQty;
    }

    /**
     * Reduce stock quantity
     */
    public function reduceStock($quantity)
    {
        if ($this->qty >= $quantity) {
            $this->decrement('qty', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase stock quantity
     */
    public function increaseStock($quantity)
    {
        $this->increment('qty', $quantity);
        return true;
    }
}
