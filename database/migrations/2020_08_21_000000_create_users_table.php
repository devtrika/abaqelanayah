<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
  public function up() {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50);
      $table->string('country_code', 5)->default('965');
      $table->string('phone', 15);
      $table->string('email', 50)->nullable();
      $table->string('password', 100);
      $table->enum('type' , ['client' , 'delivery'])->default('client');
      $table->enum('gender', ['male', 'female'])->nullable();
      $table->boolean('is_active')->default(false);
      $table->boolean('is_blocked')->default(false);
      $table->boolean('is_notify')->default(true);
    $table->string('code', 10)->nullable();
      $table->timestamp('code_expire')->nullable();
      $table->foreignId('city_id')->nullable()->constrained()->cascadeOnDelete();
      $table->foreignId('district_id')->nullable()->constrained()->cascadeOnDelete();
      $table->decimal('wallet_balance', 10, 2)->default(0)->comment('User wallet balance');
      $table->softDeletes();
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    });
  }

  public function down() {
    Schema::dropIfExists('users');
  }
}
