<?php

namespace App\Http\Requests\Api\Cart;

use App\Http\Requests\Api\BaseApiRequest;

class ApplyCouponRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_code' => 'required|string|exists:coupons,coupon_num',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'coupon_code.required' => __('site.please_enter_coupon_code'),
            'coupon_code.exists'   => __('cart.invalid_coupon_code'),
        ];
    }
}
