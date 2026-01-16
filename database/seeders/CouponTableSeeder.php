<?php
namespace Database\Seeders;


use App\Models\Coupon;
use Illuminate\Database\Seeder;
use DB;

class CouponTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coupons')->insert([
            [
                'coupon_name' => 'first',
                'coupon_num'      => 'QWERT' , 
                'type'          => 'ratio',
                'discount'      => 10,
                'max_discount'  => 20,
                
                'start_date'   => \Carbon\Carbon::now()->addDays(2),
                'expire_date'   => \Carbon\Carbon::now()->addDays(10),
            ] , [
                'coupon_name' => 'first2',

                'coupon_num'      => 'JAKA' , 
                'type'          => 'number',
                'discount'      => 20,
                'max_discount'  => 20,
               
                'start_date'   => \Carbon\Carbon::now()->addDays(2),
                'expire_date'   => \Carbon\Carbon::now()->addDays(10),
            ] , [
                'coupon_name' => 'first4',

                'coupon_num'      => 'UsageEnd' , 
                'type'          => 'ratio',
                'discount'      => 10,
                'max_discount'  => 20,
               
                'start_date'   => \Carbon\Carbon::now(),
                'expire_date'   => \Carbon\Carbon::now()->addDays(1),
            ] , [
                'coupon_name' => 'first5',

                'coupon_num'      => 'Expire' , 
                'type'          => 'number',
                'discount'      => 10,
                'max_discount'  => 10,
              
                'start_date'   => \Carbon\Carbon::now(),
                'expire_date'   => \Carbon\Carbon::now(),
            ]
        ]);
    }
}
