<?php
namespace App\Services;

use App\Models\Coupon;

class CouponService {

    static function checkCoupon($coupon_num, $total_price = null ){
        if(!$coupon = Coupon::whereCouponNum($coupon_num)->first()){
            return [ 'msg' => __('apis.not_avilable_coupon') , 'key' => 'fail' ];
        }
        if ($coupon->status == 'closed' ) {
            return [ 'msg' => __('apis.not_avilable_coupon') , 'key' => 'fail' ];
        }
        if ($coupon->status == 'usage_end') {
            return [ 'msg' => __('apis.max_usa_coupon') , 'key' => 'fail' ];
        }

        if ($coupon->expire_date < today() || $coupon->status == 'expire' ) {
            return [ 'msg' => __('apis.coupon_end_at' , ['date' =>  date('d-m-Y  h:i A', strtotime($coupon->expire_date)) ]) , 'key' => 'fail' ];
        }

        if ($coupon->start_date > today() ) {
            return [ 'msg' => __('apis.coupon_start_at' , ['date' =>  date('d-m-Y  h:i A', strtotime($coupon->start_date)) ]) , 'key' => 'fail' ];
        }

        if ('ratio' == $coupon->type) {
            $disc_amount = $coupon->discount * $total_price / 100;
            if ($disc_amount > $coupon->max_discount) {
                $disc_amount = $coupon->max_discount;
            }
        } else {
            $disc_amount = $coupon->discount;
        }


        return [
            'msg' => __('apis.disc_amount') . ' ' . $disc_amount . ' ' . __('apis.rs'),
            'key' => 'success',
            'data' => [
                'disc_amount' => $disc_amount,
                'coupon' => $coupon->only(['type', 'discount', 'id'])
            ]
        ];
    }
}