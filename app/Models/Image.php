<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;


class Image extends BaseModel implements HasMedia
{

    public const IMAGEPATH = 'storage/images/';

    use InteractsWithMedia , HasTranslations;
    protected $fillable = ['name' , 'is_active' , 'link','type'];
    public $translatable = ['name'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image_ar')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));

        $this->addMediaCollection('image_en')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    public function getImageArAttribute()
    {
        return $this->getFirstMediaUrl('image_ar');
    }

    public function getImageEnAttribute()
    {
        return $this->getFirstMediaUrl('image_en');
    }
}
