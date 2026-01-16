<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Blog extends BaseModel implements HasMedia
{
    use HasTranslations , InteractsWithMedia;
    protected $fillable = ['category_id','title' ,'content' , 'is_active'];
    public $translatable = ['title','content'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('blogs')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    /**
     * Get the category that owns the blog
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the blog image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('blogs') ?: asset('storage/images/default.png');
    }

    /**
     * Get the comments for the blog
     */
    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get the reactions for the blog
     */
    public function reactions()
    {
        return $this->hasMany(BlogReaction::class);
    }

    /**
     * Get the likes count
     */
    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('reaction', 'like')->count();
    }

    /**
     * Get the dislikes count
     */
    public function getDislikesCountAttribute()
    {
        return $this->reactions()->where('reaction', 'dislike')->count();
    }

    /**
     * Get the comments count (only approved comments)
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->where('is_approved', true)->count();
    }

    /**
     * Determine if the current user has liked the blog
     */
    public function getIsLikedAttribute()
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return false;
        }
        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('reaction', 'like')
            ->exists();
    }

    /**
     * Determine if the current user has disliked the blog
     */
    public function getIsDislikedAttribute()
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return false;
        }
        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('reaction', 'dislike')
            ->exists();
    }


    
}
