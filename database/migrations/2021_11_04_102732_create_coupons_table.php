<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
             $table->string('code')->nullable()->unique();
            $table->string('coupon_name');
            $table->string('coupon_num');
            $table->foreignId(column: 'provider_id')->nullable()->constrained('providers')->cascadeOnDelete();
            $table->enum('type', ['ratio', 'number']);
            $table->double('discount');
            $table->double('max_discount')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('expire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
