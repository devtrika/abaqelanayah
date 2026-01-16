<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\Course;
use App\Facades\Responder;
use App\Models\CourseStage;
use Illuminate\Http\Request;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CourseProgressController extends Controller
{
    /**
     * Get course progress for authenticated user
     */


    /**
     * Update watch time for a stage and optionally mark as completed
     */
    public function updateWatchTime(Request $request, $courseId, $stageId)
    {
        try {
            $request->validate([
                'last_watch_time' => 'required|string|regex:/^\d{1,2}:\d{2}:\d{2}$/', // Time in H:i:s format
                'time_spent' => 'nullable|integer|min:0|max:86400', // Max 24 hours in seconds
                'completed' => 'nullable|boolean' // 1 to mark as completed
            ]);

            $user = auth()->user();
            $enrollment = $user->courseEnrollments()
                              ->where('course_id', $courseId)
                              ->whereIn('status', ['active', 'pending_payment'])
                              ->first();

            if (!$enrollment) {
                return Responder::error('You are not enrolled in this course', [], 404);
            }

            // Check if stage exists and belongs to the course
            $stage = CourseStage::where('id', $stageId)
                               ->where('course_id', $courseId)
                               ->first();

            if (!$stage) {
                return Responder::error('Stage not found or does not belong to this course', [], 404);
            }

            DB::beginTransaction();

            try {
                $watchTimeString = $request->input('last_watch_time');
                $timeSpent = $request->input('time_spent');
                $completed = $request->input('completed', false);

                // Get the stage completion record
                $completion = $enrollment->stageCompletions()
                                        ->where('stage_id', $stageId)
                                        ->first();

                if (!$completion) {
                    return Responder::error('Stage completion record not found', [], 404);
                }

                // Store H:i:s format directly in database
                $updateData = ['last_watch_time' => $watchTimeString];

                if ($timeSpent !== null) {
                    $updateData['time_spent'] = $timeSpent;
                }

                // If completed flag is sent as 1, mark as completed
                if ($completed) {
                    $updateData['completed_at'] = now();
                }

                // Update the completion record
                $completion->update($updateData);

                // Update total time spent if provided
                if ($timeSpent !== null) {
                    $enrollment->update(['total_time_spent' => $enrollment->stageCompletions()->sum('time_spent')]);
                }

                // Recalculate progress if stage was completed
                if ($completed) {
                    $enrollment->calculateProgress();
                }

                DB::commit();

                Log::info('Stage updated successfully', [
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'stage_id' => $stageId,
                    'last_watch_time' => $watchTimeString,
                    'time_spent' => $timeSpent,
                    'completed' => $completed
                ]);

                $freshCompletion = $completion->fresh();

                return Responder::success([
                    'stage_updated' => true,
                    'last_watch_time' => $freshCompletion->last_watch_time,
                    'time_spent' => $freshCompletion->time_spent,
                    'is_completed' => $freshCompletion->completed_at !== null,
                    'completed_at' => $freshCompletion->completed_at,
                    'progress_percentage' => $enrollment->fresh()->progress_percentage,
                    'total_time_spent' => $enrollment->fresh()->total_time_spent
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return Responder::error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error updating stage', [
                'course_id' => $courseId,
                'stage_id' => $stageId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return Responder::error('Failed to update stage', [], 500);
        }
    }
}
