<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at');
            $table->enum('status', ['pending_payment', 'active', 'suspended', 'completed', 'cancelled'])->default('pending_payment');
            $table->decimal('progress_percentage', 5, 2)->default(0)->comment('Course completion percentage');
            $table->integer('total_time_spent')->default(0)->comment('Total time spent in seconds');
            $table->timestamp('last_accessed_at')->nullable()->comment('Last time student accessed the course');
            $table->integer('completed_stages_count')->default(0)->comment('Number of completed stages');
            $table->timestamp('completed_at')->nullable();

            // Payment information
            $table->enum('payment_method', ['wallet', 'bank_transfer', 'credit_card', 'mada', 'apple_pay']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_reference')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->timestamp('payment_completed_at')->nullable();

            $table->timestamps();

            // Ensure user can only enroll once per course
            $table->unique(['user_id', 'course_id']);

            // Add indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['payment_status']);
            $table->index(['progress_percentage']);
            $table->index(['last_accessed_at']);
            $table->index(['completed_stages_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
