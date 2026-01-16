<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends BaseModel 
{
    
    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'latitude',
        'longitude',
        'polygon',
        'delivery_type',
        'expected_duration',
        'last_order_time',
        'status'
    ];

    public function workingHours()
    {
        return $this->hasMany(BranchWorkingHour::class);
    }

    public function deliveryHours()
    {
        return $this->hasMany(BranchDeliveryHour::class);
    }
    
    public function managers()
    {
        return $this->belongsToMany(Admin::class, 'branch_managers', 'branch_id', 'manager_id');
    }
    
    public function deliveries()
    {
        return $this->belongsToMany(User::class, 'branch_deliveries', 'branch_id', 'delivery_id');
    }

      // Relations
   
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
