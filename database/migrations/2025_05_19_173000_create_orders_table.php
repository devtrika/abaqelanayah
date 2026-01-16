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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->enum('delivery_type', ['immediate', 'scheduled']);
            $table->date('schedule_date')->nullable();
            $table->time('schedule_time')->nullable();
            $table->enum('order_type', ['ordinary', 'gift']);
            $table->foreignId('delivery_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('wallet_deduction', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
