<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseEnrollmentBankTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected CourseEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'type' => 'admin' // Assuming admin users have type 'admin'
        ]);
        
        // Create regular user
        $user = User::factory()->create();
        
        // Create course
        $course = Course::factory()->create();
        
        // Create enrollment with bank transfer
        $this->enrollment = CourseEnrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => 5, // bank transfer
            'bank_transfer_status' => 'pending',
            'payment_status' => 'pending',
            'status' => 'pending_payment',
            'sender_bank_name' => 'البنك الأهلي السعودي',
            'sender_account_holder_name' => 'أحمد محمد علي',
            'sender_account_number' => '1234567890',
            'sender_iban' => 'SA1234567890123456789012',
            'transfer_amount' => 100.00,
            'transfer_reference' => 'REF123456'
        ]);
    }

    /** @test */
    public function admin_can_view_enrollment_with_bank_data()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertSee('البنك الأهلي السعودي');
        $response->assertSee('أحمد محمد علي');
        $response->assertSee('1234567890');
        $response->assertSee('SA1234567890123456789012');
        $response->assertSee('100.00');
        $response->assertSee('REF123456');
        $response->assertSee('في الانتظار'); // pending status in Arabic
    }

    /** @test */
    public function admin_can_verify_bank_transfer()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id), [
            'admin_notes' => 'تم التحقق من التحويل بنجاح'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم تأكيد التحويل البنكي بنجاح'
        ]);

        $this->enrollment->refresh();

        $this->assertEquals('verified', $this->enrollment->bank_transfer_status);
        $this->assertEquals('paid', $this->enrollment->payment_status);
        $this->assertEquals('active', $this->enrollment->status);
        $this->assertEquals($this->admin->id, $this->enrollment->verified_by);
        $this->assertEquals('تم التحقق من التحويل بنجاح', $this->enrollment->admin_notes);
        $this->assertNotNull($this->enrollment->verified_at);
        $this->assertNotNull($this->enrollment->payment_completed_at);
    }

    /** @test */
    public function admin_can_reject_bank_transfer()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.rejectBankTransfer', $this->enrollment->id), [
            'admin_notes' => 'بيانات التحويل غير صحيحة'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم رفض التحويل البنكي'
        ]);

        $this->enrollment->refresh();

        $this->assertEquals('rejected', $this->enrollment->bank_transfer_status);
        $this->assertEquals('failed', $this->enrollment->payment_status);
        $this->assertEquals('cancelled', $this->enrollment->status);
        $this->assertEquals($this->admin->id, $this->enrollment->rejected_by);
        $this->assertEquals('بيانات التحويل غير صحيحة', $this->enrollment->admin_notes);
        $this->assertNotNull($this->enrollment->rejected_at);
    }

    /** @test */
    public function admin_cannot_verify_non_bank_transfer_enrollment()
    {
        // Create wallet payment enrollment
        $walletEnrollment = CourseEnrollment::factory()->create([
            'payment_method_id' => 6, // wallet
            'payment_status' => 'paid',
            'status' => 'active'
        ]);

        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $walletEnrollment->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'هذا الاشتراك ليس بتحويل بنكي'
        ]);
    }

    /** @test */
    public function admin_cannot_verify_already_processed_transfer()
    {
        // Mark enrollment as already verified
        $this->enrollment->update([
            'bank_transfer_status' => 'verified',
            'payment_status' => 'paid',
            'status' => 'active'
        ]);

        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'تم معالجة هذا التحويل مسبقاً'
        ]);
    }

    /** @test */
    public function admin_can_see_verification_details_in_view()
    {
        // Verify the transfer first
        $this->enrollment->verifyBankTransfer($this->admin->id, 'تم التحقق بنجاح');

        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertSee('تم التحقق من التحويل');
        $response->assertSee($this->admin->name);
        $response->assertSee('تم التحقق بنجاح');
    }

    /** @test */
    public function admin_can_see_rejection_details_in_view()
    {
        // Reject the transfer first
        $this->enrollment->rejectBankTransfer($this->admin->id, 'بيانات غير صحيحة');

        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertSee('تم رفض التحويل');
        $response->assertSee($this->admin->name);
        $response->assertSee('بيانات غير صحيحة');
    }

    /** @test */
    public function pending_transfers_show_action_buttons()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertSee('تأكيد التحويل');
        $response->assertSee('رفض التحويل');
        $response->assertSee('verifyTransfer(' . $this->enrollment->id . ')');
        $response->assertSee('rejectTransfer(' . $this->enrollment->id . ')');
    }
}
