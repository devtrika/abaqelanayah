<?php

namespace App\Http\Controllers\Api\Client;

use App\Facades\Responder;
use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\CourseResource;
use App\Http\Requests\Api\Course\EnrollCourseRequest;
use App\Http\Requests\Api\Course\ConfirmCoursePaymentRequest;
use App\Models\CourseEnrollment;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function __construct(protected CourseService $courseService){

    }

    public function index(Request $request)
    {
        // Accept 'sort' query parameter: 'old_to_new' or 'new_to_old'
        $sort = $request->input('sort', 'new_to_old');
        $data = $this->courseService->index($sort);
        return Responder::success(CourseResource::collection($data));
    }

    public function show($id)
    {
        $user = auth()->user();
        $course = $this->courseService->getCourseWithEnrollmentStatus($id, $user?->id);

        // Load stages with user completion data if user is authenticated
        if ($user) {
            $course->load(['stages.userCompletion']);
        } else {
            $course->load('stages');
        }

        return Responder::success(CourseResource::make($course));
    }

    /**
     * Enroll user in course
     */
    public function enroll(EnrollCourseRequest $request, $id)
    {
        try {
            $user = auth()->user();
            $result = $this->courseService->enrollInCourse($user, $id, $request->validated());

            if ($result['status_code'] === 200) {
                return response()->json([
                    'status_code' => 200,
                    'message' => $result['message'],
                    'data' => $result['data']
                ], 200);
            } else {
                return response()->json([
                    'status_code' => $result['status_code'],
                    'message' => $result['message'],
                    'data' => $result['data'] ?? null
                ], $result['status_code']);
            }
        } catch (\Exception $e) {
            // Rollback any database changes
            DB::rollBack();

            // Log the error for debugging
            Log::error('Course enrollment failed', [
                'user_id' => auth()->id(),
                'course_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status_code' => 500,
                'message' => 'An error occurred during enrollment. Please try again.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get user's enrolled courses
     */
    public function myEnrollments()
    {
        $user = auth()->user();
        $courses = $this->courseService->getUserEnrolledCourses($user->id);
        return Responder::success(CourseResource::collection($courses));
    }

    /**
     * Confirm course payment (for admin or webhook)
     */
    public function confirmPayment($enrollmentId, ConfirmCoursePaymentRequest $request)
    {
        try {
            $enrollment = CourseEnrollment::findOrFail($enrollmentId);
            $result = $this->courseService->confirmCoursePayment($enrollment, $request->payment_reference);

            return Responder::success($result, ['message' => 'Payment confirmed successfully']);
        } catch (\Exception $e) {
            // Rollback any database changes
            DB::rollBack();

            // Log the error for debugging
            Log::error('Course payment confirmation failed', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Responder::error('An error occurred while confirming payment. Please try again.');
        }
    }

    /**
     * Get available payment gateways for course enrollment
     */
    public function paymentGateways(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);
            $gateways = [
                ['code' => 'card', 'name' => 'Credit Card (MyFatoorah)'],
            ];
            return Responder::success([
                'course_id' => $course->id,
                'course_price' => $course->price,
                'gateways' => $gateways,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get course payment gateways', [
                'course_id' => $id,
                'error' => $e->getMessage()
            ]);

            return Responder::error('Failed to get payment gateways');
        }
    }
}
