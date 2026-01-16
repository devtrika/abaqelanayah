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
        Schema::create('provider_sub_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->string('sub_order_number')->unique(); // PSO-20250103-ABC123
            $table->string('status')->default('pending_payment'); // pending_payment, processing, confirmed, completed, cancelled
            
            // Financial breakdown for this provider
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('services_total', 10, 2)->default(0);
            $table->decimal('products_total', 10, 2)->default(0);
            $table->decimal('booking_fee', 10, 2)->default(0);
            $table->decimal('home_service_fee', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
         
            
            $table->timestamps();

            // Indexes
            $table->unique(['order_id', 'provider_id']);
            $table->index(['order_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index('sub_order_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_sub_orders');
    }
};
