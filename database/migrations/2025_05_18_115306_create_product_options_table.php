<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['weight', 'cutting', 'packaging']);
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_options');
    }
}