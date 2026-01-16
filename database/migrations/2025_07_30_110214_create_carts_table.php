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
      Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);

            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_value', 10, 2)->nullable();
            $table->decimal('wallet_deduction', 10, 2)->default(0);

            $table->unsignedInteger('loyalty_points_used')->default(0);
            $table->decimal('loyalty_points_value', 10, 2)->nullable();

            $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
