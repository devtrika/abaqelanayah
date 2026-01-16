<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginVerificationCode extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at', 'is_blocked', 'attempts'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isBlocked()
    {
        return $this->is_blocked;
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
    /**
     * Store a new verification code for the user.
     *
     * @param int $userId
     * @param string $code
     * @param \Carbon\Carbon|string $expiresAt
     * @return static
     */
    public static function storeCode($userId, $code, $expiresAt = null)
    {
        if (!$expiresAt) {
            $expiresAt = now()->addMinutes(5);
        }
        return self::create([
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => $expiresAt,
            'is_blocked' => false,
            'attempts' => 0,
            ]);
    }
}
