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
        // Drop old rating tables after data migration
        Schema::dropIfExists('provider_rates');
        Schema::dropIfExists('service_rates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate provider_rates table
        Schema::create('provider_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rate');
            $table->text('body');
            $table->timestamps();
        });

        // Recreate service_rates table
        Schema::create('service_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rate');
            $table->text('body');
            $table->timestamps();
        });
    }
};
