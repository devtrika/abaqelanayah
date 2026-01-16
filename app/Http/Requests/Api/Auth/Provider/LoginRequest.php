<?php

namespace App\Http\Requests\Api\Auth\Provider;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Http\Requests\Api\BaseApiRequest;

class LoginRequest extends BaseApiRequest
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
            'phone'        => 'required|numeric',
            'country_code' => 'required',
            'password'     => 'required|min:6',
            'device_token' => 'nullable|string',
            'device_id'    => 'nullable|string',
            'device_type'  => 'nullable|in:android,ios,web',
        ];
    }
}