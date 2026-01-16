<?php

namespace App\Http\Requests\Admin\coupons;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'coupon_name'           => 'required|string|max:255',
            'coupon_num'            => 'required|unique:coupons,coupon_num',
            // 'provider_id'           => 'nullable|exists:providers,id',
            'type'                  => 'required|in:ratio,number',
            'discount'              => 'required|numeric|min:1',
            'max_discount'          => 'nullable|numeric|min:1',
            'usage_time'            => 'nullable|integer|min:1',
            'start_date'            => 'nullable|date|after_or_equal:today',
            'expire_date'           => 'nullable|date|after:start_date',
            'is_active'             => 'boolean',
        ];
    }

    public function messages()
    {
        return[
            'coupon_num.unique' => __('admin.coupon_number_used_before') ,
        ];
    }
}
