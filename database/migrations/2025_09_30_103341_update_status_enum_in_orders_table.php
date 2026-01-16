<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'new',
                'out-for-delivery',
                'confirmed',
                'processing',
                'delivered',
                'problem',
                'cancelled',
                'request_refund',
                'refunded',
                'request_rejected',
                'request_cancel'
            ])->default('pending')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'new',
                'out-for-delivery',
                'confirmed',
                'processing',
                'delivered',
                'problem',
                'cancelled',
                'request_refund',
                'refunded',
                'request_rejected',
            ])->default('pending')->after('user_id');
        });
    }
};
