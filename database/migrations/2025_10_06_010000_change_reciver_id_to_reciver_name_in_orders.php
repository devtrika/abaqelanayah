<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'reciver_id')) {
                try { $table->dropForeign(['reciver_id']); } catch (\Exception $e) {}
                $table->dropColumn('reciver_id');
            }
            if (!Schema::hasColumn('orders', 'reciver_name')) {
                $table->string('reciver_name')->nullable()->after('address_id');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'reciver_id')) {
                $table->unsignedBigInteger('reciver_id')->nullable()->after('address_id');
                if (Schema::hasTable('users')) {
                    $table->foreign('reciver_id')->references('id')->on('users')->onDelete('set null');
                }
            }
            if (Schema::hasColumn('orders', 'reciver_name')) {
                $table->dropColumn('reciver_name');
            }
        });
    }
};
