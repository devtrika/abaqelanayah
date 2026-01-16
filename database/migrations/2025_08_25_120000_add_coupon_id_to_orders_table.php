<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('payment_status');
                if (Schema::hasTable('coupons')) {
                    $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'coupon_id')) {
                // drop foreign key if exists
                try {
                    $table->dropForeign(['coupon_id']);
                } catch (\Exception $e) {
                    // ignore if FK not present
                }
                $table->dropColumn('coupon_id');
            }
        });
    }
};
