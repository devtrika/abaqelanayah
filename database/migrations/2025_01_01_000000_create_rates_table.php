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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('rateable'); // rateable_type and rateable_id
            $table->integer('rate')->comment('Rating value from 1 to 5');
            $table->text('body')->nullable()->comment('Review text');
            $table->timestamps();

            // Ensure a user can only rate a specific item once
            $table->unique(['user_id', 'rateable_type', 'rateable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
