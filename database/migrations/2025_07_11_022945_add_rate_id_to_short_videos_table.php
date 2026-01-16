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
        Schema::table('short_videos', function (Blueprint $table) {
            $table->foreignId('rate_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_rate_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_videos', function (Blueprint $table) {
            $table->dropForeign(['rate_id']);
            $table->dropColumn('rate_id');
            });
    }
};
