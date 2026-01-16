<?php

namespace App\Services;

use App\Facades\Responder;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService
{

    public function __construct(
        protected Course $model
    ){

    }

    public function index($sort = 'new_to_old')
    {
        $query = $this->model->where('is_active', true);
        if ($sort === 'old_to_new') {
            $query = $query->orderBy('created_at', 'asc');
        } else {
            $query = $query->orderBy('created_at', 'desc');
        }
        return $query->get();
    }

    public function getById($id)
    {
        $course =  $this->model->find($id);
        if(!$course)
        {
            return Responder::error('not found');
        }
        return $course;
    }

    /**
     * Enroll user in course by purchasing it
     *
     * @param User $user
     * @param int $courseId
     * @param array $paymentData
     * @return array
     */
    public function enrollInCourse(User $user, int $courseId, array $paymentData)
    {
        try {
            return DB::transaction(function () use ($user, $courseId, $paymentData) {
            // Get the course
            $course = Course::find($courseId);
            if (!$course || !$course->is_active) {
                return [
                    'status_code' => 404,
                    'message' => 'Course not found or not available',
                    'data' => null
                ];
            }

            // Check for existing enrollment
            $existingEnrollment = $this->getExistingEnrollment($user->id, $courseId);
            if ($existingEnrollment) {
                return $this->handleExistingEnrollment($existingEnrollment, $paymentData);
            }

            // Create enrollment record with payment info
            $enrollment = CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'status' => 'pending_payment',
                'payment_method_id' => $paymentData['payment_method_id'],
                'payment_status' => 'pending',
                'amount_paid' => $course->price,
            ]);

            // Create stage completion records for all course stages
            $enrollment->createStageCompletionRecords();

            // Process payment
            $paymentResult = $this->processCoursePayment($enrollment, $user, $paymentData);

            if (!is_array($paymentResult) || !isset($paymentResult['status_code'])) {
                // Log unexpected payment result structure
                Log::error('Unexpected payment result structure', [
                    'payment_result' => $paymentResult,
                    'enrollment_id' => $enrollment->id,
                    'payment_method_id' => $paymentData['payment_method_id']
                ]);

                $enrollment->delete();
                return [
                    'status_code' => 500,
                    'message' => 'Payment processing error. Please try again.',
                    'data' => null
                ];
            }

            if ($paymentResult['status_code'] === 200) {
                return [
                    'status_code' => 200,
                    'message' => $paymentResult['message'],
                    'data' => $paymentResult['data']
                ];
            } else {
                // Payment failed, delete the enrollment
                $enrollment->delete();
                return [
                    'status_code' => $paymentResult['status_code'],
                    'message' => $paymentResult['message'],
                    'data' => null
                ];
            }
            });
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            // Log the error
            Log::error('Course enrollment service error', [
                'user_id' => $user->id,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status_code' => 500,
                'message' => 'An error occurred during enrollment. Please try again.',
                'data' => null
            ];
        }
    }

    /**
     * Process payment for course enrollment
     */
    private function processCoursePayment(CourseEnrollment $enrollment, User $user, array $paymentData)
    {
        try {
            $result = null;

            switch ($paymentData['payment_method_id']) {
                case 6: // wallet
                    $result = $this->processWalletPayment($enrollment, $user);
                    break;
                case 1: // credit_card
                case 2: // credit_card
                case 3: // mada
                case 4: // apple_pay
                    $result = $this->processElectronicPayment($enrollment, $paymentData);
                    break;
                case 5: // bank_transfer
                    $result = $this->processBankTransferPayment($enrollment, $paymentData);
                    break;
                default:
                    $result = [
                        'status_code' => 400,
                        'message' => 'Invalid payment method',
                        'data' => null
                    ];
                    break;
            }

            // Ensure result has the expected structure
            if (!is_array($result) || !isset($result['status_code'])) {
                Log::error('Payment method returned invalid structure', [
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'result' => $result,
                    'enrollment_id' => $enrollment->id
                ]);

                return [
                    'status_code' => 500,
                    'message' => 'Payment processing error',
                    'data' => null
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Payment processing exception', [
                'payment_method_id' => $paymentData['payment_method_id'],
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'status_code' => 500,
                'message' => 'Payment processing failed',
                'data' => null
            ];
        }
    }

    /**
     * Process wallet payment for course
     */
    private function processWalletPayment(CourseEnrollment $enrollment, User $user)
    {
        if ($user->wallet_balance >= $enrollment->amount_paid) {
            // Deduct from wallet
            $user->decrement('wallet_balance', $enrollment->amount_paid);

            // Mark payment as completed
            $enrollment->markPaymentAsCompleted('WALLET-' . time());

            // Process loyalty points
            $this->processLoyaltyPoints($user, $enrollment->amount_paid);

            return [
                'status_code' => 200,
                'message' => 'Payment successful',
                'data' => [
                    'enrollment_id' => $enrollment->id
                ]
            ];
        }

        return [
            'status_code' => 402,
            'message' => 'Insufficient wallet balance',
            'data' => null
        ];
    }

    /**
     * Process bank transfer payment for course
     */
    private function processBankTransferPayment(CourseEnrollment $enrollment, array $paymentData)
    {
        // Store bank transfer data from request
        $this->storeBankTransferData($enrollment, $paymentData);

        // For bank transfer, enrollment stays pending until admin confirms
        $enrollment->update([
            'payment_reference' => 'BANK-COURSE-' . $enrollment->id,
        ]);

        return [
            'status_code' => 200,
            'message' => 'Enrollment created successfully. Bank transfer data saved.',
            'data' => [
                'enrollment_id' => $enrollment->id
            ]
        ];
    }

    /**
     * Process electronic payment for course using MyFatoorah
     */
    private function processElectronicPayment(CourseEnrollment $enrollment, array $paymentData)
    {
        try {
            $myfatoorahService = app(\App\Services\Myfatoorah\CoursePaymentService::class);

            $user = $enrollment->user;
            $course = $enrollment->course;

            // Create payment invoice with MyFatoorah
            $result = $myfatoorahService->createCoursePaymentInvoice(
                $enrollment,
                $user,
                $course,
                [
                    'gateway' => $paymentData['gateway'] ?? 'myfatoorah'
                ]
            );

            if ($result['success']) {
                return [
                    'status_code' => 200,
                    'message' => 'Redirect to MyFatoorah payment gateway',
                    'data' => [
                        'enrollment_id' => $enrollment->id,
                        'payment_url' => $result['invoice_url']
                    ]
                ];
            } else {
                return [
                    'status_code' => 400,
                    'message' => $result['message'],
                    'data' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Electronic payment processing failed', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'status_code' => 500,
                'message' => 'Payment gateway error. Please try again.',
                'data' => null
            ];
        }
    }

    /**
     * Get existing enrollment for user and course
     */
    private function getExistingEnrollment(int $userId, int $courseId): ?CourseEnrollment
    {
        return CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * Handle existing enrollment based on its status
     */
    private function handleExistingEnrollment(CourseEnrollment $existingEnrollment, array $paymentData)
    {
        switch ($existingEnrollment->status) {
            case 'active':
            case 'completed':
                return [
                    'status_code' => 409,
                    'message' => 'User is already enrolled in this course',
                    'data' => [
                        'enrollment_id' => $existingEnrollment->id
                    ]
                ];

            case 'pending_payment':
                // Check if payment method is the same, if so, return existing enrollment
                if ($existingEnrollment->payment_method_id === $paymentData['payment_method_id']) {
                    // Try to process payment again for existing enrollment
                    $paymentResult = $this->processCoursePayment($existingEnrollment, $existingEnrollment->user, $paymentData);

                    return $paymentResult;
                } else {
                    // Different payment method, update the enrollment
                    $existingEnrollment->update([
                        'payment_method_id' => $paymentData['payment_method_id'],
                        'bank_account_id' => $paymentData['bank_account_id'] ?? null,
                        'enrolled_at' => now(),
                    ]);

                    $paymentResult = $this->processCoursePayment($existingEnrollment, $existingEnrollment->user, $paymentData);

                    return $paymentResult;
                }

            case 'cancelled':
            case 'failed':
                // Reactivate the enrollment with new payment method
                $existingEnrollment->update([
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'bank_account_id' => $paymentData['bank_account_id'] ?? null,
                    'enrolled_at' => now(),
                    'payment_reference' => null,
                    'payment_completed_at' => null,
                ]);

                $paymentResult = $this->processCoursePayment($existingEnrollment, $existingEnrollment->user, $paymentData);

                return $paymentResult;

            default:
                return [
                    'status_code' => 400,
                    'message' => 'Invalid enrollment status',
                    'data' => null
                ];
        }
    }

    /**
     * Check if user is enrolled in course
     */
    public function isUserEnrolled(int $userId, int $courseId): bool
    {
        return CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('payment_status' , 'paid')

            ->whereIn('status', ['active', 'completed'])
            ->exists();
    }

    /**
     * Get user's enrolled courses
     */
    public function getUserEnrolledCourses(int $userId)
    {
        return CourseEnrollment::with(['course.stages.userCompletion'])
            ->where('user_id', $userId)
            ->where('payment_status' , 'paid')
            ->whereIn('status', ['active', 'completed'])
            ->get()
            ->pluck('course');
    }

    /**
     * Get course with enrollment status for user
     */
    public function getCourseWithEnrollmentStatus(int $courseId, ?int $userId = null)
    {
        $course = $this->getById($courseId);

        if ($userId) {
            $course->is_enrolled = $this->isUserEnrolled($userId, $courseId);
            $enrollment = CourseEnrollment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();
            $course->enrollment = $enrollment;
        } else {
            $course->is_enrolled = false;
            $course->enrollment = null;
        }

        return $course;
    }

    /**
     * Process loyalty points for course purchase
     */
    private function processLoyaltyPoints(User $user, float $amountPaid)
    {
        $pointsEarned = $user->calculateLoyaltyPointsEarned($amountPaid);
        if ($pointsEarned > 0) {
            $user->addLoyaltyPoints($pointsEarned);
        }
    }

    /**
     * Store bank transfer data from request
     */
    private function storeBankTransferData(CourseEnrollment $enrollment, array $paymentData)
    {
        $bankData = [
            'sender_bank_name' => $paymentData['sender_bank_name'] ?? null,
            'sender_account_holder_name' => $paymentData['sender_account_holder_name'] ?? null,
            'sender_account_number' => $paymentData['sender_account_number'] ?? null,
            'sender_iban' => $paymentData['sender_iban'] ?? null,
            'transfer_amount' => $paymentData['transfer_amount'] ?? null,
            'transfer_reference' => $paymentData['transfer_reference'] ?? null,
            'transfer_date' => isset($paymentData['transfer_date']) ?
                \Carbon\Carbon::parse($paymentData['transfer_date'])->format('Y-m-d') :
                now()->format('Y-m-d'),
            'bank_transfer_status' => 'pending',
        ];

        // Filter out null values
        $bankData = array_filter($bankData, function($value) {
            return $value !== null;
        });

        $enrollment->update($bankData);

        Log::info('Bank transfer data stored for course enrollment', [
            'enrollment_id' => $enrollment->id,
            'bank_data' => $bankData
        ]);

        return $enrollment;
    }

    /**
     * Confirm payment for course enrollment (called by payment gateway webhook or admin)
     */
    public function confirmCoursePayment(CourseEnrollment $enrollment, ?string $paymentReference = null)
    {
        try {
            return DB::transaction(function () use ($enrollment, $paymentReference) {
                $enrollment->markPaymentAsCompleted($paymentReference);

                // Process loyalty points for electronic payments
                if (in_array($enrollment->payment_method_id, [2, 3, 4])) {
                    $this->processLoyaltyPoints($enrollment->user, $enrollment->amount_paid);
                }

                return $enrollment;
            });
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();

            // Log the error
            Log::error('Course payment confirmation error', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to confirm course payment. Please try again.');
        }
    }

    /**
     * Update enrollment status directly (for webhook processing)
     */
    public function updateEnrollmentStatus(CourseEnrollment $enrollment, string $status, string $paymentStatus, ?string $paymentReference = null)
    {
        try {
            return DB::transaction(function () use ($enrollment, $status, $paymentStatus, $paymentReference) {
                $updateData = [
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                ];

                if ($paymentReference) {
                    $updateData['payment_reference'] = $paymentReference;
                }

                if ($paymentStatus === 'paid') {
                    $updateData['payment_completed_at'] = now();

                    // Process loyalty points for successful payments
                    $this->processLoyaltyPoints($enrollment->user, $enrollment->amount_paid);
                }

                $enrollment->update($updateData);

                Log::info('Enrollment status updated', [
                    'enrollment_id' => $enrollment->id,
                    'status' => $status,
                    'payment_status' => $paymentStatus
                ]);

                return $enrollment->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update enrollment status', [
                'enrollment_id' => $enrollment->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to update enrollment status: ' . $e->getMessage());
        }
    }
}