<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchDeliveryHour extends Model
{
    protected $fillable = [
        'branch_id',
        'day',
        'start_time',
        'end_time',
        'is_working'
    ];

    protected $casts = [
        'is_working' => 'boolean',
    ];

    /**
     * Get the branch that owns the delivery hours
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to get delivery hours for a specific day
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day', $day);
    }

    /**
     * Scope to get only working days
     */
    public function scopeWorking($query)
    {
        return $query->where('is_working', true);
    }
}