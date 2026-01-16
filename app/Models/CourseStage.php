<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseStage extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'course_id',
        'title',
        'order'
    ];

    public $translatable = ['title'];

    protected $casts = [
        'order' => 'integer'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stage-videos')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/avi', 'video/mov', 'video/wmv']);
    }

    /**
     * Get the course that owns the stage
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all stage completions for this stage
     */
    public function stageCompletions()
    {
        return $this->hasMany(CourseStageCompletion::class, 'stage_id');
    }

    /**
     * Get the stage completion for the current authenticated user
     */
    public function userCompletion()
    {
        return $this->hasOne(CourseStageCompletion::class, 'stage_id')
                    ->whereHas('enrollment', function ($query) {
                        $query->where('user_id', auth()->id());
                    });
    }

    /**
     * Get the stage video
     */
    public function getVideoAttribute()
    {
        return $this->getFirstMediaUrl('stage-videos');
    }

    /**
     * Get the video file name
     */
    public function getVideoNameAttribute()
    {
        $media = $this->getFirstMedia('stage-videos');
        return $media ? $media->name : null;
    }
}
