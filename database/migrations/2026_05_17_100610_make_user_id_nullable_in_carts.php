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
            $table->string('session_id')->nullable()->after('user_id');
        });
        
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE carts MODIFY user_id bigint unsigned NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE carts MODIFY user_id bigint unsigned NOT NULL');
    }
};
