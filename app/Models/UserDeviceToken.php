<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'device_id',
        'app_version',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active tokens only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tokens by device type.
     */
    public function scopeByDeviceType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Mark token as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate the token.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Create or update device token for user.
     */
    public static function createOrUpdate(int $userId, string $deviceToken, array $attributes = []): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'device_token' => $deviceToken,
            ],
            array_merge([
                'is_active' => true,
                'last_used_at' => now(),
            ], $attributes)
        );
    }

    /**
     * Get all active tokens for a user.
     */
    public static function getActiveTokensForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->active()
            ->get();
    }

    /**
     * Clean up inactive tokens older than specified days.
     */
    public static function cleanupInactiveTokens(int $days = 30): int
    {
        return static::where('is_active', false)
            ->where('updated_at', '<', now()->subDays($days))
            ->delete();
    }
}
