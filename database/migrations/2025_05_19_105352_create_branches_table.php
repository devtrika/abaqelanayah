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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
          $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('latitude');
            $table->string('longitude');
            $table->json('polygon')->nullable();
            $table->enum('delivery_type', ['normal', 'express'])->default('normal');
            $table->integer('expected_duration')->nullable()->comment('المدة المتوقعة (بالدقائق)');
            $table->time('last_order_time')->nullable()->comment('آخر موعد للطلب ');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
