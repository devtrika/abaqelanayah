<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class IntroSocialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('intro_socials')->insert([
            [
                'icon'             => 'fab fa-facebook-f',
                'key'              => 'facebook',
                'url'              => 'https://www.facebook.com',
            ],[
                'icon'             => 'fab fa-twitter',
                'key'              => 'twitter',
                'url'              => 'https://www.twitter.com',
            ],[
                'icon'             => 'fab fa-instagram',
                'key'              => 'instagram',
                'url'              => 'https://www.instagram.com',
            ],[
                'icon'             => 'fab fa-linkedin-in',
                'key'              => 'linkedin',
                'url'              => 'https://www.linkedin.com',
            ]
        ]);
    }
}
