<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProviderRate extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['user_id' , 'provider_id' , 'rate' , 'body'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('rates-images')
            ->useFallbackUrl(asset('storage/images/providers/default.png'))
            ->useFallbackPath(public_path('storage/images/providers/default.png'));
    }

    public function getRatesImagesUrlsAttribute()
    {
        return $this->getMedia('rates-images')->map(function ($media) {
            return $media->getUrl();
        });
    }



}
