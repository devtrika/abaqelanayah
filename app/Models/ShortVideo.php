<?php
namespace App\Models;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortVideo extends BaseModel implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'video_id',
        'order_rate_id',
        'rate_id',
        'client_name',
        'published_at',
        'expired_at',
        'is_active',
        'user_id'
    ];

    // Media collection for video
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('short_video')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo'])
            ->useDisk('public'); // Adjust if using a different disk
    }

    public function orderRate()
    {
        return $this->belongsTo(OrderRate::class , 'order_rate_id' , 'id');
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class ,'user_id','id')->withTrashed();
    }
}
