<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rate extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'rateable_type',
        'rateable_id',
        'rate',
        'body',
        'status',
    ];

    protected $casts = [
        'rate' => 'integer',
    ];

    /**
     * Get the user who made the rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the rateable model (Provider, Product, etc.)
     */
    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Register media collections for rating images/videos
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rate-media')
            ->useFallbackUrl(asset('storage/images/rates/default.png'))
            ->useFallbackPath(public_path('storage/images/rates/default.png'));
    }

    /**
     * Get all media URLs for this rating
     */
    public function getMediaUrlsAttribute()
    {
        return $this->getMedia('rate-media')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'type' => $media->mime_type,
                'name' => $media->name,
            ];
        });
    }

    /**
     * Scope to filter by rating value
     */
    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rate', $rating);
    }

    /**
     * Scope to filter by rateable type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('rateable_type', $type);
    }
}
