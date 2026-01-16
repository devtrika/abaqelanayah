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
        Schema::create('courses', function (Blueprint $table) {
     
            $table->id();
            $table->json('name'); // Arabic and English names
            $table->json('instructor_name'); // Arabic and English instructor names
            $table->decimal('duration', 8, 2); // Duration in hours
            $table->json('description'); // Arabic and English descriptions
            $table->decimal('price', 10, 2); // Course price
            $table->boolean('is_active')->default(true); // Visibility for customers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
