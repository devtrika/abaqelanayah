<?php

namespace App\Services\Myfatoorah;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\Myfatoorah\PaymentMyfatoorahApiV2;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class CoursePaymentService
{
    protected $myfatoorahApi;
    protected $config;

    public function __construct()
    {
        $this->config = config('myfatoorah');
        $this->myfatoorahApi = new PaymentMyfatoorahApiV2(
            $this->config['api_key'],
            $this->config['test_mode'],
            $this->config['log_enabled'] ? $this->config['log_file'] : null
        );
    }

    /**
     * Create payment invoice for course enrollment
     */
    public function createCoursePaymentInvoice(CourseEnrollment $enrollment, User $user, Course $course, array $options = [])
    {
        try {
            // Ensure proper amount formatting
            $invoiceValue = (float) $enrollment->amount_paid;

            // Get clean phone number
            $cleanPhone = $this->cleanPhoneNumber($user->phone ?? '');

            // Ensure we have required data
            if (empty($cleanPhone)) {
                throw new Exception('Valid phone number is required for payment');
            }

            if ($invoiceValue <= 0) {
                throw new Exception('Invalid payment amount');
            }

            $postFields = [
                'NotificationOption' => $this->config['notification_option'],
                'InvoiceValue' => $invoiceValue,
                'CustomerName' => $user->name ?? 'Course Student',
                'DisplayCurrencyIso' => $this->config['currency'],
                'MobileCountryCode' => $this->config['country_code'],
                'CustomerMobile' => $cleanPhone,
                'CallBackUrl' => route('payment.webhook'),
                'ErrorUrl' => route('payment.webhook'),

                'Language' => $this->config['language'],
                'CustomerReference' => (string) $enrollment->id, // Ensure string format
                'UserDefinedField' => 'course_enrollment', // Identify this as course payment
                'CustomerEmail' => $user->email ?? 'noemail@example.com',
                'InvoiceItems' => [
                    [
                        'ItemName' => $course->name ?? 'Course Enrollment',
                        'Quantity' => 1,
                        'UnitPrice' => $invoiceValue,
                        'Weight' => 0,
                        'Width' => 0,
                        'Height' => 0,
                        'Depth' => 0,
                    ]
                ],
            ];

            // Log the payment request for debugging
            Log::info('Creating MyFatoorah invoice', [
                'enrollment_id' => $enrollment->id,
                'amount' => $invoiceValue,
                'currency' => $this->config['currency'],
                'phone' => $cleanPhone,
                'test_mode' => $this->config['test_mode'],
                'gateway' => $options['gateway'] ?? 'myfatoorah'
            ]);

            // Get payment gateway
            $gateway = $options['gateway'] ?? 'myfatoorah';

            // Create invoice
            $result = $this->myfatoorahApi->getInvoiceURL($postFields, $gateway, $enrollment->id);

            // Update enrollment with payment reference
            $enrollment->update([
                'payment_reference' => $result['invoiceId'],
            ]);

            return [
                'success' => true,
                'invoice_url' => $result['invoiceURL'],
                'invoice_id' => $result['invoiceId'],
                'enrollment_id' => $enrollment->id,
            ];

        } catch (Exception $e) {
            Log::error('MyFatoorah course payment creation failed', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment invoice: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment status for course enrollment
     */
    public function verifyCoursePayment($paymentId, $enrollmentId = null)
    {
        try {
            $responseData = $this->myfatoorahApi->getPaymentStatus($paymentId, 'PaymentId');

            // Convert to array for easier handling
            $responseArray = json_decode(json_encode($responseData), true);

            // Verify this is a course payment
            if ($responseArray['UserDefinedField'] !== 'course_enrollment') {
                throw new Exception('Invalid payment type');
            }

            // Get enrollment ID from customer reference
            $enrollmentIdFromResponse = $responseArray['CustomerReference'];

            // Verify enrollment ID matches if provided
            if ($enrollmentId && $enrollmentIdFromResponse != $enrollmentId) {
                throw new Exception('Enrollment ID mismatch');
            }

            // Find the enrollment
            $enrollment = CourseEnrollment::find($enrollmentIdFromResponse);
            if (!$enrollment) {
                throw new Exception('Enrollment not found');
            }

            // Check payment status
            if ($responseArray['focusTransaction']['TransactionStatus'] === 'Succss') {
                return $this->handleSuccessfulPayment($enrollment, $responseArray);
            } else {
                return $this->handleFailedPayment($enrollment, $responseArray);
            }

        } catch (Exception $e) {
            Log::error('MyFatoorah course payment verification failed', [
                'payment_id' => $paymentId,
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(CourseEnrollment $enrollment, array $responseData)
    {
        return DB::transaction(function () use ($enrollment, $responseData) {
            // Update enrollment status
            $enrollment->update([
                'payment_status' => 'paid',
                'status' => 'active',
                'payment_reference' => $responseData['focusTransaction']['PaymentId'],
                'payment_completed_at' => now(),
            ]);

            // Process loyalty points if applicable
            $user = $enrollment->user;
            if (method_exists($user, 'addLoyaltyPoints')) {
                $pointsEarned = $user->calculateLoyaltyPointsEarned($enrollment->amount_paid);
                if ($pointsEarned > 0) {
                    $user->addLoyaltyPoints($pointsEarned);
                }
            }

            Log::info('Course payment successful', [
                'enrollment_id' => $enrollment->id,
                'payment_id' => $responseData['focusTransaction']['PaymentId'],
                'amount' => $enrollment->amount_paid
            ]);

            return [
                'success' => true,
                'message' => 'Payment successful',
                'enrollment' => $enrollment->fresh(),
                'payment_data' => $responseData
            ];
        });
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(CourseEnrollment $enrollment, array $responseData)
    {
        $enrollment->update([
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        Log::warning('Course payment failed', [
            'enrollment_id' => $enrollment->id,
            'payment_status' => $responseData['InvoiceStatus'],
            'error' => $responseData['InvoiceError'] ?? 'Unknown error'
        ]);

        return [
            'success' => false,
            'message' => 'Payment failed: ' . ($responseData['InvoiceError'] ?? 'Unknown error'),
            'enrollment' => $enrollment->fresh(),
            'payment_data' => $responseData
        ];
    }

    /**
     * Clean phone number for MyFatoorah
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading zeros
        $phone = ltrim($phone, '0');

        // Remove country code if present
        if (str_starts_with($phone, '966')) {
            $phone = substr($phone, 3);
        }

        // Ensure we have a valid Saudi phone number (9 digits starting with 5)
        if (strlen($phone) === 9 && str_starts_with($phone, '5')) {
            return $phone;
        }

        // If phone doesn't match expected format, return empty (will trigger validation error)
        return '';
    }

    /**
     * Get available payment gateways for courses
     */
    public function getAvailableGateways($amount = 0)
    {
        try {
            $gateways = $this->myfatoorahApi->getVendorGateways($amount, $this->config['currency']);

            // Filter to only course-supported gateways
            $supportedGateways = collect($gateways)->filter(function ($gateway) {
                return in_array($gateway->PaymentMethodCode, $this->config['course_gateways']);
            });

            return [
                'success' => true,
                'gateways' => $supportedGateways->values()->toArray()
            ];

        } catch (Exception $e) {
            Log::error('Failed to get MyFatoorah gateways', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get payment gateways',
                'gateways' => []
            ];
        }
    }
}
