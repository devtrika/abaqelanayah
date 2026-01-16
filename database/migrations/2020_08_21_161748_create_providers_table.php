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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('salon_type', ['salon', 'beauty_center']);
            $table->json('commercial_name');
            $table->string('commercial_register_no'); // 10 digits
            $table->string('institution_name');
            $table->string('sponsor_name')->nullable(); // for non-Saudis
            $table->string('sponsor_phone')->nullable(); // for non-Saudis
            $table->boolean('is_mobile')->default(false);
            $table->decimal('mobile_service_fee', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['in_review', 'pending','accepted', 'rejected', 'deleted' , 'blocked'])->default('in_review');
            $table->boolean('is_active')->default(false);
            $table->boolean('accept_orders')->default(false);
            $table->decimal('wallet_balance', 12, 2)->default(0);
            $table->decimal('withdrawable_balance', 12, 2)->default(0);
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('map_desc', 50)->nullable();
            $table->string('nationality')->nullable();
            $table->string('residence_type')->nullable(); // for non-Saudis 
            $table->boolean('in_home')->default(false);
            $table->boolean('in_salon')->default(false);       
            $table->integer('home_fees')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
