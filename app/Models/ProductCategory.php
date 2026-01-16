<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductCategory extends Model implements HasMedia
{
    use HasTranslations , InteractsWithMedia;

    protected $fillable = ['name','is_active'];
    public $translatable = ['name'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product-categories')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    /**
     * Register media conversions for product categories
     */
    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(500)
            ->height(500)
            ->nonQueued();
    }

    /**
     * Get product category image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('product-categories');
    }

    /**
     * Get product category image URL with conversion
     */
    public function getImageUrl($conversion = null)
    {
        if ($conversion) {
            return $this->getFirstMediaUrl('product-categories', $conversion);
        }
        return $this->getFirstMediaUrl('product-categories');
    }

    /**
     * Get the products for the category
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'category_id');
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch($query, $searchArray = [])
    {
        if ($searchArray && !empty(array_filter($searchArray))) {
            $query->where(function ($query) use ($searchArray) {
                foreach ($searchArray as $key => $value) {
                    if ($key == 'name' && !empty($value)) {
                        $query->where(function($q) use ($value) {
                            $q->where('name->ar', 'like', '%' . $value . '%')
                              ->orWhere('name->en', 'like', '%' . $value . '%');
                        });
                    } elseif ($key == 'is_active' && $value !== '' && $value !== null) {
                        $query->where('is_active', $value);
                    } elseif ($key == 'created_at_min' && !empty($value)) {
                        $query->whereDate('created_at', '>=', $value);
                    } elseif ($key == 'created_at_max' && !empty($value)) {
                        $query->whereDate('created_at', '<=', $value);
                    } elseif ($key == 'order') {
                        // Skip order parameter
                    }
                }
            });
        }

        return $query->orderBy('created_at', request()->searchArray && request()->searchArray['order'] ? request()->searchArray['order'] : 'DESC');
    }

}
