<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends BaseModel implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'name',
        'instructor_name',
        'duration',
        'description',
        'price',
        'is_active'
    ];

    public $translatable = ['name', 'instructor_name', 'description'];

    protected $casts = [
        'duration' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('courses')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));
    }

    /**
     * Get the stages for the course
     */
    public function stages()
    {
        return $this->hasMany(CourseStage::class)->orderBy('order');
    }

    /**
     * Get the enrollments for the course
     */
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get the enrolled users for the course
     */
    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'status', 'payment_status', 'progress_percentage', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the stages count attribute
     */
    public function getStagesCountAttribute()
    {
        return $this->stages()->count();
    }

    /**
     * Get the course image
     */
    public function getImageAttribute()
    {
        return $this->getFirstMedia('courses') ?: asset('storage/images/default.png');
    }

    /**
     * Check if the authenticated user owns (is enrolled in) this course
     */
    public function getIsOwnedByUserAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->enrollments()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'completed'])
            ->where('payment_status', 'paid')
            ->exists();
    }

    /**
     * Get the authenticated user's enrollment for this course
     */
    public function getUserEnrollmentAttribute()
    {
        if (!auth()->check()) {
            return null;
        }

        return $this->enrollments()
            ->where('user_id', auth()->id())
            ->first();
    }

    /**
     * Check if the authenticated user can access this course
     */
    public function getCanAccessAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        $enrollment = $this->user_enrollment;

        return $enrollment &&
               $enrollment->payment_status === 'paid' &&
               in_array($enrollment->status, ['active', 'completed']);
    }

    /**
     * Get the authenticated user's progress percentage for this course
     */
    public function getUserProgressAttribute()
    {
        if (!auth()->check()) {
            return 0;
        }

        $enrollment = $this->user_enrollment;

        if (!$enrollment) {
            return 0;
        }

        // Recalculate progress to ensure accuracy with new implementation
        $enrollment->calculateProgress();

        return $enrollment->progress_percentage;
    }

    /**
     * Check if a specific user owns this course
     */
    public function isOwnedByUser($userId)
    {
        return $this->enrollments()
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->where('payment_status', 'paid')
            ->exists();
    }

    /**
     * Get enrollment for a specific user
     */
    public function getEnrollmentForUser($userId)
    {
        return $this->enrollments()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get active enrollments count
     */
    public function getActiveEnrollmentsCountAttribute()
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->count();
    }

    /**
     * Get completed enrollments count
     */
    public function getCompletedEnrollmentsCountAttribute()
    {
        return $this->enrollments()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->count();
    }

    /**
     * Get total enrollments count (paid only)
     */
    public function getTotalEnrollmentsCountAttribute()
    {
        return $this->enrollments()
            ->where('payment_status', 'paid')
            ->count();
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
                            $q->where('name->ar', 'like', "%{$value}%")
                              ->orWhere('name->en', 'like', "%{$value}%");
                        });
                    } elseif ($key == 'instructor_name' && !empty($value)) {
                        $query->where(function($q) use ($value) {
                            $q->where('instructor_name->ar', 'like', "%{$value}%")
                              ->orWhere('instructor_name->en', 'like', "%{$value}%");
                        });
                    } elseif ($key == 'price_from' && !empty($value)) {
                        $query->where('price', '>=', $value);
                    } elseif ($key == 'price_to' && !empty($value)) {
                        $query->where('price', '<=', $value);
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

    public function transactions()
{
    return $this->morphMany(Transaction::class, 'transactionable');
}
}
