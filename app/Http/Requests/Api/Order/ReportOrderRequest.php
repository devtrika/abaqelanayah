<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\BaseApiRequest;

class ReportOrderRequest extends BaseApiRequest
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
            'note' => 'required|string|max:1000',
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
            'note.required' => __('apis.report_note_required'),
            'note.string' => __('apis.report_note_must_be_string'),
            'note.max' => __('apis.report_note_max_length'),
        ];
    }
}
