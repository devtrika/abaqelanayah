<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTranslations , SoftDeletes;

    public $translatable = ['commercial_name'];

    protected $fillable = [
        'user_id',
        'provider_type',
        'salon_type',
        'commercial_name',
        'commercial_register_no',
        'institution_name',
        'sponsor_name',
        'sponsor_phone',
        'is_mobile',
        'mobile_service_fee',
        'description',
        'status',
        'rejection_reason',
        'accept_orders',
        'wallet_balance',
        'withdrawable_balance',
        'nationality',
        'lat',
        'lng',
        'comission',
        'map_desc',
        'nationality',
        'residence_type',
        'in_home',
        'in_salon',
        'home_fees',
        'is_active'
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
        'in_home' => 'boolean',
        'in_salon' => 'boolean',
        'accept_orders' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'withdrawable_balance' => 'decimal:2',
        'home_fees' => 'decimal:2',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    /**
     * Register media collections for the provider
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));

        $this->addMediaCollection('commercial_register_image')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));

        $this->addMediaCollection('residence_image')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));

        $this->addMediaCollection('salon_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    /**
     * Register media conversions for the provider
     */
    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->nonQueued();
    }

    /**
     * Get the logo URL attribute
     */
    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('logo');
    }

    /**
     * Get the residence image URL attribute
     */

     public function subOrders()
{
    return $this->hasMany(ProviderSubOrder::class, 'provider_id');
}
    public function getResidenceImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('residence_image');
    }

    public function getCommercialImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('commercial_register_image');
    }

    /**
     * Get the salon images URLs attribute
     */
    public function getSalonImagesUrlsAttribute()
    {
        return $this->getMedia('salon_images')->map(function ($media) {
            return $media->getUrl();
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function bankAccount()
    {
        return $this->hasOne(ProviderBankAccount::class);
    }

    /**
     * Get the services for the provider
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get active services for the provider
     */
    public function activeServices()
    {
        return $this->hasMany(Service::class)->where('is_active', true);
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get active services for the provider
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * Get the working hours for the provider
     */
    public function workingHours()
    {
        return $this->hasMany(ProviderWorkingHour::class);
    }

    /**
     * Get the orders for the provider
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the sub-orders for the provider
     */
    public function providerSubOrders()
    {
        return $this->hasMany(ProviderSubOrder::class);
    }

    /**
     * Check if provider is available on a specific date and time
     *
     * @param string|\Carbon\Carbon $date
     * @param string|null $from_time
     * @param string|null $to_time
     * @return bool
     */
    public function isAvailable($date = null, $from_time = null, $to_time = null)
    {
        // Use current date/time if not provided
        $checkDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $dayOfWeek = strtolower($checkDate->format('l'));

        // Get working hours for the specific day
        $workingHour = $this->workingHours()
            ->where('day', $dayOfWeek)
            ->where('is_working', true)
            ->first();

        // If no working hours found for this day, provider is not available
        if (!$workingHour) {
            return false;
        }

        // If no specific time range provided, just check if provider works on this day
        if (!$from_time && !$to_time) {
            return true;
        }

        // Check if the requested time range falls within working hours
        $workStart = $workingHour->start_time;
        $workEnd = $workingHour->end_time;

        // If only from_time provided, check if it's within working hours
        if ($from_time && !$to_time) {
            return $from_time >= $workStart && $from_time <= $workEnd;
        }

        // If only to_time provided, check if it's within working hours
        if (!$from_time && $to_time) {
            return $to_time >= $workStart && $to_time <= $workEnd;
        }

        // If both times provided, check if the entire range is within working hours
        return $from_time >= $workStart && $to_time <= $workEnd && $from_time < $to_time;
    }

    public function getIsCurrentlyAvailableAttribute()
    {
        $now = \Carbon\Carbon::now();
        return $this->isAvailable($now, $now->format('H:i'));
    }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    /**
     * Get all favorites for this provider
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Check if the provider is favorited by the current authenticated user
     *
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->favorites()
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * Get the average rating for the provider
     *
     * @return float
     */
    public function getAverageRateAttribute()
    {
        return $this->rates()->avg('rate') ?? 0;
    }

    /**
     * Get the total count of ratings for the provider
     *
     * @return int
     */
    public function getRatesCountAttribute()
    {
        return $this->rates()->count();
    }

    /**
     * Get formatted average rating (rounded to 1 decimal place)
     *
     * @return float
     */
    public function getFormattedAverageRateAttribute()
    {
        return round($this->average_rate, 1);
    }

    /**
     * Get rating statistics
     *
     * @return array
     */
    public function getRatingStatsAttribute()
    {
        $rates = $this->rates()->pluck('rate');

        if ($rates->isEmpty()) {
            return [
                'average' => 0,
                'count' => 0,
                'distribution' => [
                    '5' => 0,
                    '4' => 0,
                    '3' => 0,
                    '2' => 0,
                    '1' => 0,
                ]
            ];
        }

        return [
            'average' => round($rates->avg(), 1),
            'count' => $rates->count(),
            'distribution' => [
                '5' => $rates->where('rate', 5)->count(),
                '4' => $rates->where('rate', 4)->count(),
                '3' => $rates->where('rate', 3)->count(),
                '2' => $rates->where('rate', 2)->count(),
                '1' => $rates->where('rate', 1)->count(),
            ]
        ];
    }



    public function rateSummary(): Attribute
    {
        return Attribute::make(
            get: function () {
                $average = $this->rates()->avg('rate') ?? 0;
                $count = $this->rates()->count();

                return [
                    'average' => round($average, 2),
                    'count' => $count,
                ];
            }
        );
    }

    /**
     * Calculate distance between provider and authenticated user
     * Returns distance in kilometers
     */
    public function distanceFromUser(): Attribute
    {
        return Attribute::make(
            get: function () {
                $user = Auth::user();

                // Return null if no authenticated user or missing location data
                if (!$user || !$user->lat || !$user->lng || !$this->lat || !$this->lng) {
                    return null;
                }

                return $this->calculateDistance(
                    (float) $user->lat,
                    (float) $user->lng,
                    (float) $this->lat,
                    (float) $this->lng
                );
            }
        );
    }

    /**
     * Calculate distance between provider and specific coordinates
     * Returns distance in kilometers
     */
    public function distanceFrom(float $lat, float $lng): float
    {
        if (!$this->lat || !$this->lng) {
            return 0;
        }

        return $this->calculateDistance($lat, $lng, (float) $this->lat, (float) $this->lng);
    }

    /**
     * Calculate distance between two points using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }


public function withdrawRequests()
{
    return $this->hasMany(WithdrawRequest::class);
}

public function views()
{
    return $this->hasMany(View::class);
}

public function getViewsCountAttribute()
{
    return $this->views->count();
}


}