<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\BaseApiRequest;

class CancelOrderRequest extends BaseApiRequest
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
            // Allow either a predefined reason or free-text notes (mutually exclusive)
            'cancel_reason_id' => 'nullable|exists:cancel_reasons,id|required_without:notes',
            'notes' => 'nullable|string|max:1000|required_without:cancel_reason_id',
        ];
    }



}
