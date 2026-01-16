<?php

namespace App\Http\Requests\Api\Cart;

use App\Http\Requests\Api\BaseApiRequest;

class ApplyLoyaltyPointsRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'points' => 'required|integer|min:1',
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
            'points.required' => 'Points amount is required',
            'points.integer' => 'Points must be a valid number',
            'points.min' => 'Points must be at least 1',
        ];
    }
}
