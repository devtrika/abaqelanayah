<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{

  public function run()
  {

    $faker = Faker::create('ar_SA');
    $users = [];
    for ($i = 0; $i < 10; $i++) {
      $users[] = [
        'name' => $faker->name,
        'phone' => "51111111$i",
        'email' => $faker->unique()->email,
        'type' => Arr::random(['client', 'delivery']),
        'gender' => Arr::random(['male', 'female']),

        'password' => bcrypt(123456),
        'is_active' => rand(0, 1),
      
        'created_at' => now(),
        'updated_at' => now(),
      ];
    }

    DB::table('users')->insert($users);
  }
}
