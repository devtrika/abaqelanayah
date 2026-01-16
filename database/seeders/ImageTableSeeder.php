<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            [
                'image'          => '1.png' ,
            ] , [
                'image'          => '2.png' , 
            ],[
                'image'          => '3.png' , 
            ],[
                'image'          => '4.png' , 
            ]
        ]);
    }
}
