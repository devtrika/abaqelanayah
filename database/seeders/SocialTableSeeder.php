<?php
namespace Database\Seeders;

use App\Models\Social;
use Illuminate\Database\Seeder;
use DB;

class SocialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('socials')->insert([
            [
                'icon'              => 'facebook.png',
                'name'              => 'facebook',
                'link'              => 'https://www.facebook.com',
            ],[
                'name'              => 'instagram',
                'icon'              => 'Instagram.png',
                'link'              => 'https://www.instgram.com',
            ],[
                'name'              => 'twitter',
                'icon'              => 'twitter.png',
                'link'              => 'https://www.twitter.com',
            ]
            ,[
                'name'              => 'youtube',
                'icon'              => 'youtube.png',
                'link'              => 'https://www.youtube.com',
            ]
            ,[
                'name'              => 'telegram',
                'icon'              => 'telegram.png',
                'link'              => 'https://www.telegram.com',
            ]
        ]);
    }
}
