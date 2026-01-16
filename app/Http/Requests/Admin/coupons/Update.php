<?php

namespace App\Http\Requests\Admin\coupons;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_name'           => 'required|string|max:255',
            'coupon_num'            => 'required|unique:coupons,coupon_num,'.$this->id,
            // 'provider_id'           => 'nullable|exists:providers,id',
            'type'                  => 'required|in:ratio,number',
            'discount'              => 'required|numeric|min:0',
            'max_discount'          => 'nullable|numeric|min:0',
            'usage_time'            => 'nullable|integer|min:1',
            'start_date'            => 'nullable|date',
            'expire_date'           => 'nullable|date|after:start_date',
            'is_active'             => 'boolean',
        ];
    }

    public function messages()
    {
        return[
            'coupon_num.unique' => __('admin.coupon_number_used_before'),
        ];
    }
}
