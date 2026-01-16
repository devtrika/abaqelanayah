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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->unsignedBigInteger('city_id');
            // $table->foreign('city_id')->references('country_id')->on('cities')->onDelete('cascade');
            $table->enum('advertiser_class',['marketer','advertiser'])->nullable();
            $table->bigInteger('advertiser_number')->nullable();
            $table->string('image')->nullable();
            $table->double('lat');
            $table->double('lang');
            $table->string('password');
            $table->bigInteger('ads_count')->default(0);
            $table->bigInteger('available_advs')->default(0);
            $table->uuid('uuid')->unique()->nullable();
            $table->integer('pin_code')->nullable();
            $table->timestamp('verified_at')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
