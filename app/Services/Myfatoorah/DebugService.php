<?php

namespace App\Services\Myfatoorah;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\Myfatoorah\PaymentMyfatoorahApiV2;
use Illuminate\Support\Facades\Log;

class DebugService
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
     * Debug MyFatoorah configuration and test payment
     */
    public function debugPaymentIssue(CourseEnrollment $enrollment)
    {
        $debugInfo = [
            'enrollment_id' => $enrollment->id,
            'timestamp' => now()->toISOString(),
        ];

        try {
            // 1. Check configuration
            $debugInfo['config'] = $this->checkConfiguration();

            // 2. Check user data
            $debugInfo['user_data'] = $this->checkUserData($enrollment->user);

            // 3. Check course data
            $debugInfo['course_data'] = $this->checkCourseData($enrollment->course);

            // 4. Check enrollment data
            $debugInfo['enrollment_data'] = $this->checkEnrollmentData($enrollment);

            // 5. Test MyFatoorah connection
            $debugInfo['myfatoorah_connection'] = $this->testMyfatoorahConnection();

            // 6. Test payment gateway availability
            $debugInfo['payment_gateways'] = $this->testPaymentGateways($enrollment->amount_paid);

            // 7. Create test invoice with minimal data
            $debugInfo['test_invoice'] = $this->createTestInvoice($enrollment);

            Log::info('MyFatoorah Debug Report', $debugInfo);

            return $debugInfo;

        } catch (\Exception $e) {
            $debugInfo['error'] = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];

            Log::error('MyFatoorah Debug Failed', $debugInfo);
            return $debugInfo;
        }
    }

    /**
     * Check MyFatoorah configuration
     */
    private function checkConfiguration()
    {
        return [
            'api_key_set' => !empty($this->config['api_key']),
            'api_key_length' => strlen($this->config['api_key'] ?? ''),
            'test_mode' => $this->config['test_mode'],
            'currency' => $this->config['currency'],
            'country_code' => $this->config['country_code'],
            'language' => $this->config['language'],
            'notification_option' => $this->config['notification_option'],
            'log_enabled' => $this->config['log_enabled'],
        ];
    }

    /**
     * Check user data validity
     */
    private function checkUserData(User $user)
    {
        $cleanPhone = $this->cleanPhoneNumber($user->phone ?? '');
        
        return [
            'user_id' => $user->id,
            'name' => $user->name ?? 'N/A',
            'email' => $user->email ?? 'N/A',
            'phone_original' => $user->phone ?? 'N/A',
            'phone_cleaned' => $cleanPhone,
            'phone_valid' => !empty($cleanPhone),
            'has_name' => !empty($user->name),
            'has_email' => !empty($user->email),
            'has_phone' => !empty($user->phone),
        ];
    }

    /**
     * Check course data validity
     */
    private function checkCourseData(Course $course)
    {
        return [
            'course_id' => $course->id,
            'name' => $course->name ?? 'N/A',
            'price' => $course->price ?? 0,
            'is_active' => $course->is_active ?? false,
            'has_name' => !empty($course->name),
            'valid_price' => ($course->price ?? 0) > 0,
        ];
    }

    /**
     * Check enrollment data validity
     */
    private function checkEnrollmentData(CourseEnrollment $enrollment)
    {
        return [
            'enrollment_id' => $enrollment->id,
            'amount_paid' => $enrollment->amount_paid,
            'payment_method' => $enrollment->payment_method,
            'payment_status' => $enrollment->payment_status,
            'status' => $enrollment->status,
            'payment_reference' => $enrollment->payment_reference,
            'valid_amount' => ($enrollment->amount_paid ?? 0) > 0,
        ];
    }

    /**
     * Test MyFatoorah API connection
     */
    private function testMyfatoorahConnection()
    {
        try {
            // Try to get vendor gateways as a connection test
            $gateways = $this->myfatoorahApi->getVendorGateways(100, $this->config['currency']);
            
            return [
                'connection_success' => true,
                'gateways_count' => count($gateways),
                'api_response' => 'Connected successfully'
            ];
        } catch (\Exception $e) {
            return [
                'connection_success' => false,
                'error' => $e->getMessage(),
                'api_response' => 'Connection failed'
            ];
        }
    }

    /**
     * Test payment gateways availability
     */
    private function testPaymentGateways($amount)
    {
        try {
            $gateways = $this->myfatoorahApi->getVendorGateways($amount, $this->config['currency']);
            
            $gatewayInfo = [];
            foreach ($gateways as $gateway) {
                $gatewayInfo[] = [
                    'id' => $gateway->PaymentMethodId ?? 'N/A',
                    'code' => $gateway->PaymentMethodCode ?? 'N/A',
                    'name_en' => $gateway->PaymentMethodEn ?? 'N/A',
                    'name_ar' => $gateway->PaymentMethodAr ?? 'N/A',
                    'is_direct' => $gateway->IsDirectPayment ?? false,
                ];
            }

            return [
                'success' => true,
                'total_gateways' => count($gateways),
                'gateways' => $gatewayInfo
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create test invoice with minimal data
     */
    private function createTestInvoice(CourseEnrollment $enrollment)
    {
        try {
            $user = $enrollment->user;
            $course = $enrollment->course;
            $cleanPhone = $this->cleanPhoneNumber($user->phone ?? '');

            // Use test data if user data is invalid
            $testData = [
                'NotificationOption' => 'Lnk',
                'InvoiceValue' => max(1, (float) $enrollment->amount_paid), // Minimum 1 SAR
                'CustomerName' => $user->name ?: 'Test Customer',
                'DisplayCurrencyIso' => $this->config['currency'],
                'MobileCountryCode' => $this->config['country_code'],
                'CustomerMobile' => $cleanPhone ?: '512345678', // Test phone if invalid
                'CallBackUrl' => route('client.course.payment.success'),
                'ErrorUrl' => route('client.course.payment.error'),
                'Language' => $this->config['language'],
                'CustomerReference' => (string) $enrollment->id,
                'UserDefinedField' => 'course_enrollment_test',
                'CustomerEmail' => $user->email ?: 'test@example.com',
            ];

            // Try to create invoice
            $result = $this->myfatoorahApi->getInvoiceURL($testData, 'myfatoorah', $enrollment->id);

            return [
                'success' => true,
                'test_data_used' => $testData,
                'invoice_created' => true,
                'invoice_id' => $result['invoiceId'] ?? 'N/A',
                'invoice_url' => $result['invoiceURL'] ?? 'N/A'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'test_data_used' => $testData ?? null
            ];
        }
    }

    /**
     * Clean phone number (same as CoursePaymentService)
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = ltrim($phone, '0');
        
        if (str_starts_with($phone, '966')) {
            $phone = substr($phone, 3);
        }
        
        if (strlen($phone) === 9 && str_starts_with($phone, '5')) {
            return $phone;
        }
        
        return '';
    }
}
