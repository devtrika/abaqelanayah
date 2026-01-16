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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            
            // Product options (stored as text since options might change)
            $table->string('weight')->nullable();
            $table->string('cutting')->nullable();
            $table->string('packaging')->nullable();
            
            // Original option IDs for reference
            $table->foreignId('weight_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('cutting_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('packaging_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            
            $table->decimal('price', 10, 2);
            $table->decimal('cutting_price', 10, 2)->default(0);
            $table->decimal('packaging_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
