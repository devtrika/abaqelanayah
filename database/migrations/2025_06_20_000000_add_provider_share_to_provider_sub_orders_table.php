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
        Schema::table('provider_sub_orders', function (Blueprint $table) {
            $table->decimal('provider_share', 10, 2)->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_sub_orders', function (Blueprint $table) {
            $table->dropColumn('provider_share');
        });
    }
}; 