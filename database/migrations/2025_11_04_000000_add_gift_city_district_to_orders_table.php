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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'gift_city_id')) {
                $table->unsignedBigInteger('gift_city_id')->nullable()->after('gift_address_name');
            }
            if (!Schema::hasColumn('orders', 'gift_districts_id')) {
                $table->unsignedBigInteger('gift_districts_id')->nullable()->after('gift_city_id');
            }

            // Add foreign keys if tables exist
            if (Schema::hasTable('cities') && !Schema::hasColumn('orders', 'gift_city_id')) {
                $table->foreign('gift_city_id')->references('id')->on('cities')->onDelete('set null');
            }
            if (Schema::hasTable('districts') && !Schema::hasColumn('orders', 'gift_districts_id')) {
                $table->foreign('gift_districts_id')->references('id')->on('districts')->onDelete('set null');
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
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('orders', 'gift_city_id')) {
                try {
                    $table->dropForeign(['gift_city_id']);
                } catch (\Exception $e) {
                    // ignore if foreign key not present
                }
            }
            if (Schema::hasColumn('orders', 'gift_districts_id')) {
                try {
                    $table->dropForeign(['gift_districts_id']);
                } catch (\Exception $e) {
                    // ignore if foreign key not present
                }
            }

            // Drop columns
            if (Schema::hasColumn('orders', 'gift_city_id')) {
                $table->dropColumn('gift_city_id');
            }
            if (Schema::hasColumn('orders', 'gift_districts_id')) {
                $table->dropColumn('gift_districts_id');
            }
        });
    }
};

