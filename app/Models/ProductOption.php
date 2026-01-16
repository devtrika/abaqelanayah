<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOption extends Model
{
    use HasFactory;

    protected $table = 'product_options';

    protected $fillable = [
        'product_id',
        'name',
        'type',
        'additional_price',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'additional_price' => 'decimal:2',
    ];

    /**
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
