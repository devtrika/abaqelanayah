<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralLink extends Model
{
    protected $fillable = [
        'user_id',
        'referral_code',
        'url',
        'referrable_id',
        'referrable_type',
    ];

    /**
     * Get the user who owns the referral link.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated product or service (polymorphic).
     */
    public function referrable()
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter links by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('referrable_type', $type);
    }

    /**
     * Accessor for the short code or extracted ref (optional).
     */
    public function getCodeAttribute()
    {
        return $this->referral_code;
    }
}
