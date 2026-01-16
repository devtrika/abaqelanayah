<?php

namespace App\Models;

class UserUpdate extends BaseModel
{
    protected $fillable = ['type','phone','code','country_code','user_id','email'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    private function activationCode()
{
    if (config('app.env') === 'production') {
        return mt_rand(11111, 99999);
    }

    // For local, staging, etc.
    return 12345;
}


    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = $this->activationCode();
    }

    /**
     * Check if the verification code is expired (10 minutes)
     */
    public function isExpired()
    {
        return $this->created_at->addMinutes(10)->isPast();
    }

    /**
     * Get the latest phone update for a user
     */
    public static function getLatestPhoneUpdate($userId)
    {
        return static::where('user_id', $userId)
            ->where('type', 'phone')
            ->latest()
            ->first();
    }
}
