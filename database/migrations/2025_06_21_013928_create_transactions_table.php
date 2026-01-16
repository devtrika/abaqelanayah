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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->string('type');
            $table->string('note');
            $table->string('reference');
              $table->foreignId('order_id')->nullable()->constrained();
            $table->string('bank_name')->nullable(); // اسم البنك المحول منه
            $table->string('account_holder_name')->nullable(); // اسم صاحب الحساب المحول منه
            $table->string('account_number')->nullable(); // رقم الحساب المحول منه
            $table->string('iban')->nullable(); // رقم الآيبان المحول منه
            $table->string('transfer_reference')->nullable(); // مرجع التحويل
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
