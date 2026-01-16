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
        // Add refund fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            // Refund tracking fields
            $table->boolean('is_refund')->default(false)->after('order_type');
            $table->foreignId('original_order_id')->nullable()->constrained('orders')->onDelete('cascade')->after('is_refund');
            $table->string('refund_number')->nullable()->unique()->after('original_order_id');
            $table->foreignId('refund_reason_id')->nullable()->constrained('refund_reasons')->onDelete('set null')->after('refund_number');
            $table->text('refund_reason_text')->nullable()->after('refund_reason_id');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_reason_text');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_amount');
            $table->timestamp('refund_approved_at')->nullable()->after('refund_requested_at');
            $table->timestamp('refund_rejected_at')->nullable()->after('refund_approved_at');
            $table->foreignId('refund_approved_by')->nullable()->constrained('admins')->onDelete('set null')->after('refund_rejected_at');
            $table->foreignId('refund_rejected_by')->nullable()->constrained('admins')->onDelete('set null')->after('refund_approved_by');
        });

        // Add refund fields to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            // Track which items are being refunded
            $table->boolean('is_refunded')->default(false)->after('total');
            $table->integer('refund_quantity')->default(0)->after('is_refunded');
            $table->decimal('refund_amount', 10, 2)->default(0)->after('refund_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['original_order_id']);
            $table->dropForeign(['refund_reason_id']);
            $table->dropForeign(['refund_approved_by']);
            $table->dropForeign(['refund_rejected_by']);
            
            $table->dropColumn([
                'is_refund',
                'original_order_id',
                'refund_number',
                'refund_reason_id',
                'refund_reason_text',
                'refund_amount',
                'refund_requested_at',
                'refund_approved_at',
                'refund_rejected_at',
                'refund_approved_by',
                'refund_rejected_by',
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_refunded',
                'refund_quantity',
                'refund_amount',
            ]);
        });
    }
};

