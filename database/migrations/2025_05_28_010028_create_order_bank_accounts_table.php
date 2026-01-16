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
        Schema::create('order_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Bank transfer details from customer
            $table->string('sender_bank_name'); // اسم البنك المحول منه
            $table->string('sender_account_holder_name'); // اسم صاحب الحساب المحول منه
            $table->string('sender_account_number'); // رقم الحساب المحول منه
            $table->string('sender_iban')->nullable(); // رقم الآيبان المحول منه
            $table->decimal('transfer_amount', 10, 2); // قيمة المبلغ المحول
            $table->string('transfer_reference')->nullable(); // مرجع التحويل
        
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bank_accounts');
    }
};
