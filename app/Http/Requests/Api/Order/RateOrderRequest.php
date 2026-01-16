<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\BaseApiRequest;

class RateOrderRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
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
            'order_id.required' => __('apis.order_id_required'),
            'order_id.exists' => __('apis.order_not_found'),
            'rate.required' => __('apis.rate_required'),
            'rate.numeric' => __('apis.rate_must_be_numeric'),
            'rate.min' => __('apis.rate_min_value'),
            'rate.max' => __('apis.rate_max_value'),
            'note.max' => __('apis.note_max_length'),
        ];
    }
}
