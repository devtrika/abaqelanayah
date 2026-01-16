<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends BaseModel implements HasMedia
{
    use HasTranslations , InteractsWithMedia  , HasSlug;


    protected $fillable = ['name','is_active','parent_id'];
    public $translatable = ['name'];


     public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('categories')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    /**
     * Register media conversions for categories
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
     * Get category image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('categories');
    }

    /**
 */
public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}




    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'category_id');
    }
/**
 * 
 * 
 */
public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}



    /**
     * Get category image URL with conversion
     */
    public function getImageUrl($conversion = null)
    {
        if ($conversion) {
            return $this->getFirstMediaUrl('categories', $conversion);
        }
        return $this->getFirstMediaUrl('categories');
    }

    /**
     * Get the services for the category
     */
    public function services()
    {
        return $this->hasMany(\App\Models\Service::class);
    }

    public function getServicesCountAttribute()
    {
        return $this->services()->count();
    }
}
