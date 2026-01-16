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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('map_desc')->nullable();
            $table->enum('status', [
    'pending',          // قيد الانتظار
    'new',              // جديد
    'out-for-delivery', // خارج للتسليم
    'confirmed',        // تم التأكيد
    'processing',       // جاري التجهيز
    'delivered',        // تم التوصيل
    'problem',          // به مشكلة
    'cancelled',        // ملغي
    'request_refund',   // طلب استرجاع
    'refunded',         // تم الاسترجاع
    'request_rejected', // تم رفض الطلب
])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
