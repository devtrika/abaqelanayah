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
            // Bank transfer data fields
            $table->string('sender_bank_name')->nullable()->after('payment_reference');
            $table->string('sender_account_holder_name')->nullable()->after('sender_bank_name');
            $table->string('sender_account_number')->nullable()->after('sender_account_holder_name');
            $table->string('sender_iban')->nullable()->after('sender_account_number');
            $table->decimal('transfer_amount', 10, 2)->nullable()->after('sender_iban');
            $table->string('transfer_reference')->nullable()->after('transfer_amount');
            $table->date('transfer_date')->nullable()->after('transfer_reference');
            $table->enum('bank_transfer_status', ['pending', 'verified', 'rejected'])->default('pending')->after('transfer_date');
            $table->timestamp('verified_at')->nullable()->after('bank_transfer_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->timestamp('rejected_at')->nullable()->after('verified_by');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->text('admin_notes')->nullable()->after('rejected_by');

            // Add indexes for better performance
            $table->index(['bank_transfer_status']);
            $table->index(['transfer_date']);
            $table->index(['verified_at']);
            $table->index(['rejected_at']);

            // Add foreign key constraints for admin users
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['rejected_by']);

            // Drop indexes
            $table->dropIndex(['bank_transfer_status']);
            $table->dropIndex(['transfer_date']);
            $table->dropIndex(['verified_at']);
            $table->dropIndex(['rejected_at']);

            // Drop columns
            $table->dropColumn([
                'sender_bank_name',
                'sender_account_holder_name',
                'sender_account_number',
                'sender_iban',
                'transfer_amount',
                'transfer_reference',
                'transfer_date',
                'bank_transfer_status',
                'verified_at',
                'verified_by',
                'rejected_at',
                'rejected_by',
                'admin_notes'
            ]);
        });
    }
};
