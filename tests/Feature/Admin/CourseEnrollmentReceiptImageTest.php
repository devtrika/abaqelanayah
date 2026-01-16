<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseEnrollmentReceiptImageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected CourseEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'type' => 'admin'
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
            'status' => 'pending_payment'
        ]);

        // Fake storage for testing
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_verify_bank_transfer_without_image()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم تأكيد التحويل البنكي بنجاح'
        ]);

        $this->enrollment->refresh();
        $this->assertEquals('verified', $this->enrollment->bank_transfer_status);
        $this->assertEquals('paid', $this->enrollment->payment_status);
        $this->assertEquals('active', $this->enrollment->status);
    }

    /** @test */
    public function admin_can_verify_bank_transfer_with_receipt_image()
    {
        $this->actingAs($this->admin, 'admin');

        // Create a fake image file
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);

        $response = $this->post(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id), [
            'receipt_image' => $image
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم تأكيد التحويل البنكي بنجاح'
        ]);

        $this->enrollment->refresh();
        
        // Check enrollment status
        $this->assertEquals('verified', $this->enrollment->bank_transfer_status);
        $this->assertEquals('paid', $this->enrollment->payment_status);
        $this->assertEquals('active', $this->enrollment->status);

        // Check that image was uploaded
        $this->assertNotNull($this->enrollment->getFirstMedia('receipt_images'));
        $this->assertNotNull($this->enrollment->receipt_image_url);
    }

    /** @test */
    public function admin_can_reject_bank_transfer()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.rejectBankTransfer', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم رفض التحويل البنكي'
        ]);

        $this->enrollment->refresh();
        $this->assertEquals('rejected', $this->enrollment->bank_transfer_status);
        $this->assertEquals('failed', $this->enrollment->payment_status);
        $this->assertEquals('cancelled', $this->enrollment->status);
    }

    /** @test */
    public function receipt_image_attributes_work_correctly()
    {
        // Create a fake image file and attach it
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        
        $this->enrollment->addMedia($image)
            ->toMediaCollection('receipt_images');

        // Test image URL attributes
        $this->assertNotNull($this->enrollment->receipt_image_url);
        $this->assertNotNull($this->enrollment->receipt_image_thumb_url);
        $this->assertNotNull($this->enrollment->receipt_image_preview_url);

        // Test that URLs contain expected paths
        $this->assertStringContains('receipt', $this->enrollment->receipt_image_url);
    }

    /** @test */
    public function admin_view_shows_receipt_image_when_available()
    {
        // Add receipt image to enrollment
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $this->enrollment->addMedia($image)->toMediaCollection('receipt_images');

        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertSee('صورة إيصال التحويل');
        $response->assertSee('showImageModal');
    }

    /** @test */
    public function admin_view_does_not_show_receipt_section_when_no_image()
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get(route('admin.course_enrollments.show', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertDontSee('صورة إيصال التحويل');
    }

    /** @test */
    public function cannot_verify_already_processed_transfer()
    {
        // Mark as already verified
        $this->enrollment->update(['bank_transfer_status' => 'verified']);

        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'تم معالجة هذا التحويل مسبقاً'
        ]);
    }

    /** @test */
    public function cannot_verify_non_bank_transfer_enrollment()
    {
        // Change to wallet payment
        $this->enrollment->update(['payment_method_id' => 6]);

        $this->actingAs($this->admin, 'admin');

        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'هذا الاشتراك ليس بتحويل بنكي'
        ]);
    }

    /** @test */
    public function unauthenticated_admin_cannot_verify_transfer()
    {
        $response = $this->postJson(route('admin.course_enrollments.verifyBankTransfer', $this->enrollment->id));

        $response->assertStatus(401);
    }

    /** @test */
    public function media_conversions_are_created()
    {
        $image = UploadedFile::fake()->image('receipt.jpg', 1200, 900);
        
        $this->enrollment->addMedia($image)
            ->toMediaCollection('receipt_images');

        $media = $this->enrollment->getFirstMedia('receipt_images');
        
        $this->assertNotNull($media);
        
        // Check that conversions exist
        $this->assertTrue($media->hasGeneratedConversion('thumb'));
        $this->assertTrue($media->hasGeneratedConversion('preview'));
    }
}
