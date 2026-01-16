<?php

namespace App\Http\Requests\Api\Cart;

use App\Http\Requests\Api\BaseApiRequest;

class AddToCartRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $itemType = $this->input('item_type');
            $itemId = $this->input('item_id');

            if ($itemType === 'product') {
                $exists = \App\Models\Product::where('id', $itemId)->exists();
                if (!$exists) {
                    $validator->errors()->add('item_id', 'Selected product does not exist');
                }
            } elseif ($itemType === 'service') {
                $exists = \App\Models\Service::where('id', $itemId)->exists();
                if (!$exists) {
                    $validator->errors()->add('item_id', 'Selected service does not exist');
                }

                // Services usually have quantity 1
                if ($this->input('quantity') > 1) {
                    $validator->errors()->add('quantity', 'Service quantity cannot exceed 1');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'item_type.required' => 'Item type is required',
            'item_type.in' => 'Item type must be either product or service',
            'item_id.required' => 'Item ID is required',
            'quantity.required' => 'Quantity is required',
            'quantity.min' => 'Quantity must be at least 1',
            'booking_time.after' => 'Booking time must be in the future',
        ];
    }
}
