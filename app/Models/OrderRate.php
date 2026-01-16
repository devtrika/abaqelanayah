<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;


class OrderRate extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['user_id' , 'status',  'order_id' , 'quality_rate' , 'timing_rate','service_rate' , 'body'];


    public function registerMediaCollections(): void
    {
        // Image collection
        $this->addMediaCollection('order-rates')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    
        // Video collection
        $this->addMediaCollection('order_rate_videos')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo']);
           
    }
    
    

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

}
