<?php

namespace App\Http\Requests\Api\Auth\Provider;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Http\Request;

class SendPhoneUpdateCodeRequest extends BaseApiRequest
{
    public function __construct(Request $request)
    {
        if (isset($request['old_phone'])) {
            $request['old_phone'] = fixPhone($request['old_phone']);
        }
        if (isset($request['new_phone'])) {
            $request['new_phone'] = fixPhone($request['new_phone']);
        }
        if (isset($request['country_code'])) {
            $request['country_code'] = fixPhone($request['country_code']);
        }
    }

    public function rules()
    {
        return [
            'country_code' => 'required|numeric|digits_between:2,5',
            'old_phone'    => 'required|numeric|digits_between:8,10',
            'new_phone'    => 'required|numeric|digits_between:8,10|different:old_phone',
        ];
    }

    public function messages()
    {
        return [
            'new_phone.different' => 'The new phone number must be different from the old phone number.',
            'old_phone.required' => 'The old phone number is required.',
            'new_phone.required' => 'The new phone number is required.',
            'country_code.required' => 'The country code is required.',
        ];
    }
}
