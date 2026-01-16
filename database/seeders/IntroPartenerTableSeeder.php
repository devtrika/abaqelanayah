<?php
namespace Database\Seeders;

use App\Models\IntroPartener;
use Illuminate\Database\Seeder;
use DB;

class IntroPartenerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('intro_parteners')->insert([
            [ 'image'   => '1.png'  ] , 
            [ 'image'  => '2.png' ] ,
            [ 'image'  => '3.png' ] ,
            [ 'image'   => '4.png' , ] 
        ]);
    }
}
