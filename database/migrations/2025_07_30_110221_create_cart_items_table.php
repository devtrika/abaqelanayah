<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            
            // Product options references
            $table->foreignId('weight_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('cutting_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            $table->foreignId('packaging_option_id')->nullable()->constrained('product_options')->onDelete('set null');
            
            $table->decimal('price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
                });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
