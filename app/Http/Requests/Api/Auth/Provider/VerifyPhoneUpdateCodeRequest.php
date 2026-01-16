<?php

namespace App\Http\Requests\Api\Auth\Provider;

use App\Http\Requests\Api\BaseApiRequest;

class VerifyPhoneUpdateCodeRequest extends BaseApiRequest
{
    public function rules()
    {
        return [
            'code' => 'required|numeric|digits:5',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'The verification code is required.',
            'code.numeric' => 'The verification code must be numeric.',
            'code.digits' => 'The verification code must be exactly 5 digits.',
        ];
    }
}
