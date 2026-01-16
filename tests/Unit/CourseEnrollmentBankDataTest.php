<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseEnrollmentBankDataTest extends TestCase
{
    use RefreshDatabase;

    protected CourseService $courseService;
    protected User $user;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->courseService = app(CourseService::class);
        
        $this->user = User::factory()->create([
            'wallet_balance' => 0 // Ensure wallet payment won't work
        ]);
        
        $this->course = Course::factory()->create([
            'price' => 100.00,
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_stores_bank_transfer_data_when_enrolling_with_bank_transfer()
    {
        $paymentData = [
            'payment_method_id' => 5, // bank_transfer
            'sender_bank_name' => 'البنك الأهلي السعودي',
            'sender_account_holder_name' => 'أحمد محمد علي',
            'sender_account_number' => '1234567890',
            'sender_iban' => 'SA1234567890123456789012',
            'transfer_amount' => 100.00,
            'transfer_reference' => 'REF123456',
            'transfer_date' => '2025-07-07'
        ];

        $result = $this->courseService->enrollInCourse($this->user, $this->course->id, $paymentData);

        $this->assertEquals(200, $result['status_code']);
        $this->assertArrayHasKey('enrollment_id', $result['data']);

        $enrollment = CourseEnrollment::find($result['data']['enrollment_id']);
        
        // Verify bank data is stored
        $this->assertEquals('البنك الأهلي السعودي', $enrollment->sender_bank_name);
        $this->assertEquals('أحمد محمد علي', $enrollment->sender_account_holder_name);
        $this->assertEquals('1234567890', $enrollment->sender_account_number);
        $this->assertEquals('SA1234567890123456789012', $enrollment->sender_iban);
        $this->assertEquals(100.00, $enrollment->transfer_amount);
        $this->assertEquals('REF123456', $enrollment->transfer_reference);
        $this->assertEquals('2025-07-07', $enrollment->transfer_date->format('Y-m-d'));
        $this->assertEquals('pending', $enrollment->bank_transfer_status);
    }

    /** @test */
    public function it_can_verify_bank_transfer()
    {
        $enrollment = CourseEnrollment::factory()->create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'payment_method_id' => 5,
            'bank_transfer_status' => 'pending',
            'payment_status' => 'pending',
            'status' => 'pending_payment'
        ]);

        $adminUser = User::factory()->create();
        $adminNotes = 'Transfer verified successfully';

        $enrollment->verifyBankTransfer($adminUser->id, $adminNotes);

        $enrollment->refresh();

        $this->assertEquals('verified', $enrollment->bank_transfer_status);
        $this->assertEquals('paid', $enrollment->payment_status);
        $this->assertEquals('active', $enrollment->status);
        $this->assertEquals($adminUser->id, $enrollment->verified_by);
        $this->assertEquals($adminNotes, $enrollment->admin_notes);
        $this->assertNotNull($enrollment->verified_at);
        $this->assertNotNull($enrollment->payment_completed_at);
    }

    /** @test */
    public function it_can_reject_bank_transfer()
    {
        $enrollment = CourseEnrollment::factory()->create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'payment_method_id' => 5,
            'bank_transfer_status' => 'pending',
            'payment_status' => 'pending',
            'status' => 'pending_payment'
        ]);

        $adminUser = User::factory()->create();
        $adminNotes = 'Invalid transfer details';

        $enrollment->rejectBankTransfer($adminUser->id, $adminNotes);

        $enrollment->refresh();

        $this->assertEquals('rejected', $enrollment->bank_transfer_status);
        $this->assertEquals('failed', $enrollment->payment_status);
        $this->assertEquals('cancelled', $enrollment->status);
        $this->assertEquals($adminUser->id, $enrollment->rejected_by);
        $this->assertEquals($adminNotes, $enrollment->admin_notes);
        $this->assertNotNull($enrollment->rejected_at);
    }

    /** @test */
    public function it_has_bank_transfer_status_check_methods()
    {
        $enrollment = CourseEnrollment::factory()->create([
            'bank_transfer_status' => 'pending'
        ]);

        $this->assertTrue($enrollment->isBankTransferPending());
        $this->assertFalse($enrollment->isBankTransferVerified());
        $this->assertFalse($enrollment->isBankTransferRejected());

        $enrollment->update(['bank_transfer_status' => 'verified']);
        $enrollment->refresh();

        $this->assertFalse($enrollment->isBankTransferPending());
        $this->assertTrue($enrollment->isBankTransferVerified());
        $this->assertFalse($enrollment->isBankTransferRejected());

        $enrollment->update(['bank_transfer_status' => 'rejected']);
        $enrollment->refresh();

        $this->assertFalse($enrollment->isBankTransferPending());
        $this->assertFalse($enrollment->isBankTransferVerified());
        $this->assertTrue($enrollment->isBankTransferRejected());
    }

    /** @test */
    public function it_has_bank_transfer_scopes()
    {
        // Create enrollments with different bank transfer statuses
        $pendingEnrollment = CourseEnrollment::factory()->create([
            'payment_method_id' => 5,
            'bank_transfer_status' => 'pending'
        ]);

        $verifiedEnrollment = CourseEnrollment::factory()->create([
            'payment_method_id' => 5,
            'bank_transfer_status' => 'verified'
        ]);

        $rejectedEnrollment = CourseEnrollment::factory()->create([
            'payment_method_id' => 5,
            'bank_transfer_status' => 'rejected'
        ]);

        // Create non-bank transfer enrollment
        $walletEnrollment = CourseEnrollment::factory()->create([
            'payment_method_id' => 6, // wallet
            'bank_transfer_status' => 'pending'
        ]);

        // Test scopes
        $pendingBankTransfers = CourseEnrollment::pendingBankTransfer()->get();
        $this->assertCount(1, $pendingBankTransfers);
        $this->assertEquals($pendingEnrollment->id, $pendingBankTransfers->first()->id);

        $verifiedBankTransfers = CourseEnrollment::verifiedBankTransfer()->get();
        $this->assertCount(1, $verifiedBankTransfers);
        $this->assertEquals($verifiedEnrollment->id, $verifiedBankTransfers->first()->id);

        $rejectedBankTransfers = CourseEnrollment::rejectedBankTransfer()->get();
        $this->assertCount(1, $rejectedBankTransfers);
        $this->assertEquals($rejectedEnrollment->id, $rejectedBankTransfers->first()->id);
    }

    /** @test */
    public function it_handles_partial_bank_data()
    {
        $paymentData = [
            'payment_method_id' => 5, // bank_transfer
            'sender_bank_name' => 'البنك الأهلي السعودي',
            'sender_account_holder_name' => 'أحمد محمد علي',
            // Missing other fields
        ];

        $result = $this->courseService->enrollInCourse($this->user, $this->course->id, $paymentData);

        $this->assertEquals(200, $result['status_code']);
        
        $enrollment = CourseEnrollment::find($result['data']['enrollment_id']);
        
        // Verify only provided data is stored
        $this->assertEquals('البنك الأهلي السعودي', $enrollment->sender_bank_name);
        $this->assertEquals('أحمد محمد علي', $enrollment->sender_account_holder_name);
        $this->assertNull($enrollment->sender_account_number);
        $this->assertNull($enrollment->sender_iban);
        $this->assertNull($enrollment->transfer_amount);
        $this->assertNull($enrollment->transfer_reference);
        $this->assertEquals('pending', $enrollment->bank_transfer_status);
    }
}
