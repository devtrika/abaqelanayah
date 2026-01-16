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
            if (!Schema::hasColumn('orders', 'reciver_id')) {
                $table->unsignedBigInteger('reciver_id')->nullable()->after('address_id');
                $table->string('reciver_phone')->nullable()->after('reciver_id');
                $table->string('gift_address_name')->nullable()->after('reciver_phone');
                $table->decimal('gift_latitude', 10, 8)->nullable()->after('gift_address_name');
                $table->decimal('gift_longitude', 11, 8)->nullable()->after('gift_latitude');
                $table->string('message')->nullable()->after('gift_longitude');
                $table->boolean('whatsapp')->default(0)->nullable()->after('message');
                $table->boolean('hide_sender')->default(0)->nullable()->after('whatsapp');

                // Add foreign key if users table exists
                if (Schema::hasTable('users')) {
                    $table->foreign('reciver_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'reciver_id')) {
                // drop foreign key if exists
                try {
                    $table->dropForeign(['reciver_id']);
                } catch (\Exception $e) {
                    // ignore if foreign key not present
                }

                $table->dropColumn([
                    'reciver_id',
                    'reciver_phone',
                    'gift_address_name',
                    'gift_latitude',
                    'gift_longitude',
                    'message',
                    'whatsapp',
                    'hide_sender',
                ]);
            }
        });
    }
};
