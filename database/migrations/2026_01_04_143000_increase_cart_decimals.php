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
        Schema::table('carts', function (Blueprint $table) {
            $table->decimal('subtotal', 20, 2)->default(0)->change();
            $table->decimal('discount', 20, 2)->nullable()->change();
            $table->decimal('total', 20, 2)->default(0)->change();
            $table->decimal('vat_amount', 20, 2)->default(0)->change();
            $table->decimal('coupon_value', 20, 2)->nullable()->change();
            $table->decimal('wallet_deduction', 20, 2)->default(0)->change();
            $table->decimal('loyalty_points_value', 20, 2)->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('price', 20, 2)->change();
            $table->decimal('discount_amount', 20, 2)->default(0)->change();
            $table->decimal('total', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->nullable()->change();
            $table->decimal('total', 10, 2)->default(0)->change();
            $table->decimal('vat_amount', 10, 2)->default(0)->change();
            $table->decimal('coupon_value', 10, 2)->nullable()->change();
            $table->decimal('wallet_deduction', 10, 2)->default(0)->change();
            $table->decimal('loyalty_points_value', 10, 2)->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('discount_amount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->change();
        });
    }
};
