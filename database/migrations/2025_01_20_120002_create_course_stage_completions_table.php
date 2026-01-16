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
        Schema::create('course_stage_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->onDelete('cascade');
            $table->unsignedBigInteger('stage_id');
            $table->timestamp('completed_at')->nullable()->comment('When the stage was completed');
            $table->integer('time_spent')->nullable()->comment('Time spent on this stage in seconds');
            $table->time('last_watch_time')->default('00:00:00')->comment('Last time watched in the video (in HH:MM:SS)');
            $table->text('notes')->nullable()->comment('Optional notes about completion');
            $table->timestamps();

            // Ensure each stage can only be completed once per enrollment
            $table->unique(['enrollment_id', 'stage_id'], 'enrollment_stage_unique');

            // Indexes for better performance
            $table->index(['enrollment_id', 'completed_at']);
            $table->index(['stage_id', 'completed_at']);
            $table->index(['enrollment_id', 'last_watch_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_stage_completions');
    }
};
