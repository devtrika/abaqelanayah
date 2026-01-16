<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStageCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'stage_id',
        'completed_at',
        'time_spent',
        'last_watch_time',
        'notes'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'time_spent' => 'integer',
    ];

    /**
     * Get the enrollment that owns the completion
     */
    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    /**
     * Get the stage that was completed
     */
    public function stage()
    {
        return $this->belongsTo(CourseStage::class, 'stage_id');
    }

    /**
     * Get the user who completed the stage
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, CourseEnrollment::class, 'id', 'id', 'enrollment_id', 'user_id');
    }

    /**
     * Get the course this completion belongs to
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseEnrollment::class, 'id', 'id', 'enrollment_id', 'course_id');
    }

    /**
     * Scope to get completions for a specific enrollment
     */
    public function scopeForEnrollment($query, $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }

    /**
     * Scope to get completions for a specific stage
     */
    public function scopeForStage($query, $stageId)
    {
        return $query->where('stage_id', $stageId);
    }

    /**
     * Scope to get completions within a date range
     */
    public function scopeCompletedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute()
    {
        if (!$this->time_spent) {
            return '0 minutes';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;

        if ($minutes > 0) {
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') .
                   ($seconds > 0 ? ' ' . $seconds . ' second' . ($seconds > 1 ? 's' : '') : '');
        }

        return $seconds . ' second' . ($seconds > 1 ? 's' : '');
    }
}
