<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseStageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        $enrollment = null;
        $completion = null;
        $isCompleted = false;
        $lastWatchTime = 0;

        // Get enrollment and completion data if user is authenticated
        if ($user) {
            $enrollment = $user->courseEnrollments()
                              ->where('course_id', $this->course_id)
                              ->first();

            if ($enrollment) {
                $completion = $enrollment->getStageCompletion($this->id);
                $isCompleted = $enrollment->isStageCompleted($this->id);
                $lastWatchTime = $completion ? $completion->last_watch_time : 0;
            }
        }

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'video_url' => $this->video_url,
            'video_duration' => $this->video_duration, // Duration in seconds
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Progress information (only if user is enrolled)
            'progress' => $enrollment ? [
                'is_completed' => $isCompleted,
                'last_watch_time' => $lastWatchTime, // Last watched time in seconds
                'completion_percentage' => $this->video_duration > 0 
                    ? round(($lastWatchTime / $this->video_duration) * 100, 2) 
                    : 0,
                'completion_details' => $completion ? [
                    'completed_at' => $completion->completed_at,
                    'time_spent' => $completion->time_spent,
                    'formatted_time_spent' => $completion->formatted_time_spent,
                    'notes' => $completion->notes
                ] : null
            ] : null,

            // Video progress information
            'video_progress' => [
                'duration_formatted' => $this->getFormattedDuration(),
                'watch_time_formatted' => $this->getFormattedWatchTime($lastWatchTime),
                'remaining_time' => max(0, $this->video_duration - $lastWatchTime),
                'remaining_time_formatted' => $this->getFormattedWatchTime(max(0, $this->video_duration - $lastWatchTime))
            ]
        ];
    }

    /**
     * Get formatted duration (HH:MM:SS or MM:SS)
     */
    private function getFormattedDuration()
    {
        return $this->formatTime($this->video_duration ?? 0);
    }

    /**
     * Get formatted watch time
     */
    private function getFormattedWatchTime($seconds)
    {
        return $this->formatTime($seconds);
    }

    /**
     * Format time in seconds to HH:MM:SS or MM:SS format
     */
    private function formatTime($seconds)
    {
        if ($seconds <= 0) {
            return '00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }

    /**
     * Additional method to get stage with enrollment context
     */
    public static function withEnrollment($stage, $enrollment = null)
    {
        $resource = new static($stage);
        $resource->enrollment = $enrollment;
        return $resource;
    }
}
