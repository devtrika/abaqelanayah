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
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Add foreign key constraint to bank_account_id now that provider_bank_accounts table exists
            $table->foreign('bank_account_id')->references('id')->on('provider_bank_accounts')->onDelete('set null');

            // Add index for better performance (only if it doesn't exist)
            if (!Schema::hasIndex('course_enrollments', 'course_enrollments_bank_account_id_index')) {
                $table->index(['bank_account_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['bank_account_id']);

            // Drop index only if it exists
            if (Schema::hasIndex('course_enrollments', 'course_enrollments_bank_account_id_index')) {
                $table->dropIndex(['bank_account_id']);
            }
        });
    }
};
