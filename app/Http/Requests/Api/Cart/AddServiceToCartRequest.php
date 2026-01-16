<?php

namespace App\Http\Requests\Api\Cart;

use App\Http\Requests\Api\BaseApiRequest;

class AddServiceToCartRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'quantity' => 'sometimes|integer|min:1|max:1', // Services usually have quantity 1
            'booking_time' => 'sometimes|date|after:now',
            'options' => 'sometimes|array',
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
            'service_id.required' => 'Service ID is required',
            'service_id.exists' => 'Selected service does not exist',
            'booking_time.after' => 'Booking time must be in the future',
            'quantity.max' => 'Service quantity cannot exceed 1',
        ];
    }
}
