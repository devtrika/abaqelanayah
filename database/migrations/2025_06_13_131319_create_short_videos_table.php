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
        Schema::create('short_videos', function (Blueprint $table) {
            $table->id();
            $table->uuid('video_id')->unique(); // Unique identifier
            $table->foreignId('order_rate_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_videos');
    }
};
