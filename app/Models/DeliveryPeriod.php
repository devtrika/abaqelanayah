<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPeriod extends Model
{
    protected $fillable = [
        'description',
    ];

    /**
     * Get orders that use this delivery period
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
