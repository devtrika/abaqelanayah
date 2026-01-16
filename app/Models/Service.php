<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\Auth;

class Service extends BaseModel
{
    use HasFactory , HasTranslations;
    public $translatable = ['name','description'];

    protected $fillable = [
        'name',
        'price',
        'duration',
        'expected_time_to_accept',
        'description',
        'is_active',
        'provider_id',
        'category_id'
    ];



    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'expected_time_to_accept' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the provider that owns the service
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the category that owns the service
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all favorites for this service
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Check if the service is favorited by the current authenticated user
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
     * Scope a query to only include active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include services for a specific provider
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Custom search scope for services with translatable fields
     */
    public function scopeSearch($query, $searchArray = [])
    {
        $query->where(function ($query) use ($searchArray) {
            if ($searchArray) {
                foreach ($searchArray as $key => $value) {
                    if (str_contains($key, '_id')) {
                        if ($value != null) {
                            $query->where($key, $value);
                        }
                    } elseif ($key == 'order') {
                        // Skip order parameter
                    } elseif ($key == 'created_at_min') {
                        if ($value != null) {
                            $query->whereDate('created_at', '>=', $value);
                        }
                    } elseif ($key == 'created_at_max') {
                        if ($value != null) {
                            $query->whereDate('created_at', '<=', $value);
                        }
                    } elseif ($key == 'name' && $value != null) {
                        // Search in translatable name field
                        $query->where(function($q) use ($value) {
                            $q->where('name->ar', 'like', "%{$value}%")
                              ->orWhere('name->en', 'like', "%{$value}%");
                        });
                    } elseif ($key == 'description' && $value != null) {
                        // Search in translatable description field
                        $query->where(function($q) use ($value) {
                            $q->where('description->ar', 'like', "%{$value}%")
                              ->orWhere('description->en', 'like', "%{$value}%");
                        });
                    } elseif ($key == 'is_active' && $value !== '' && $value !== null) {
                        $query->where('is_active', $value);
                    } else {
                        if ($value != null) {
                            $query->where($key, 'like', "%{$value}%");
                        }
                    }
                }
            }
        });
        return $query->orderBy('created_at', request()->searchArray && request()->searchArray['order'] ? request()->searchArray['order'] : 'DESC');
    }

    /**
     * Get formatted duration in hours and minutes
     */
    public function getFormattedDurationAttribute()
    {
        $hours = intval($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get formatted expected time to accept
     */
    public function getFormattedExpectedTimeAttribute()
    {
        if (!$this->expected_time_to_accept) {
            return null;
        }

        $hours = intval($this->expected_time_to_accept / 60);
        $minutes = $this->expected_time_to_accept % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }public function getInCartAttribute()
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $cart = $user->cart;
        if (!$cart) {
            return false;
        }
        return $cart->items()
            ->where('item_type', 'App\\Models\\Service')
            ->where('item_id', $this->id)
            ->exists();
    }

    


  
}
