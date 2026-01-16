<?php
namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use DB;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('countries')->insert([
            [
                'name' => json_encode(['ar' => 'الكويت' , 'en' => 'Kuwait'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '965',
                'flag'  => '🇰🇼',
                'currency_code' => 'KWD',
                'currency' => json_encode(['ar' => 'دينار كويتي' , 'en' => 'Kuwait'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
            ],
            [
                'name'          => json_encode(['ar' => 'السعودية' , 'en' => 'Saudi Arabia'], JSON_UNESCAPED_UNICODE) , 
                'key'           => '966'   , 
                'flag'          => '🇸🇦',
                'currency_code' => 'SAR',
                'currency' => json_encode(['ar' => 'ريال سعودي' , 'en' => 'SAR'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
            ] ,
            [
                'name' => json_encode(['ar' => 'البحرين' , 'en' => 'El-Bahrean'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '973'   , 
                'flag'          => '🇧🇭',
                'currency_code' => 'BHD',
                'currency' => json_encode(['ar' => 'دينار بحريني' , 'en' => 'BHD'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
    
            ] , [
                'name' => json_encode(['ar' => 'قطر' , 'en' => 'Qatar'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '974'   , 
                'flag'          => '🇶🇦',
                'currency_code' => 'QAR',
                'currency' => json_encode(['ar' => 'ريال قطري' , 'en' => 'QAR'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
            ] , [
                'name' => json_encode(['ar' => 'ليبيا' , 'en' => 'Libya'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '218'   , 
                'flag'          => '🇱🇾',
                'currency_code' => 'LYD',
                'currency' => json_encode(['ar' => 'دينار ليبي' , 'en' => 'LYD'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
    
            ] , [
                'name' => json_encode(['ar' => 'عمان' , 'en' => 'Oman'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '968'   , 
                'flag'          => '🇴🇲',
                'currency_code' => 'OMR',
                'currency' => json_encode(['ar' => 'ريال عماني' , 'en' => 'OMR'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
    
            ],[
                'name' => json_encode(['ar' => 'الامارات' , 'en' => 'UAE'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '971'   , 
                'flag'          => '🇦🇪',
                'currency_code' => 'AED',
                'currency' => json_encode(['ar' => 'درهم إماراتي' , 'en' => 'AED'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
            ] ,[
                'name' => json_encode(['ar' => 'مصر' , 'en' => 'Egypt'], JSON_UNESCAPED_UNICODE) , 
                'key'  => '20'   , 
                'flag'          => '🇪🇬',
                'currency_code' => 'EGP',
                'currency' => json_encode(['ar' => 'جنيه مصري' , 'en' => 'EGP'], JSON_UNESCAPED_UNICODE) , 
                'created_at'    => \Carbon\Carbon::now()->subMonth(rand(0,6)),
            ] ,
        ]);
    }
}
