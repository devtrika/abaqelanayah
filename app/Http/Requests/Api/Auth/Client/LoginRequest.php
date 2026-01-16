<?php

namespace App\Http\Requests\Api\Auth\Client;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Http\Requests\Api\BaseApiRequest;
use App\Rules\ClientPhoneUnique;

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
  
            
    'phone' => [
    'required',
    'regex:/^(?:966)?5\d{8}$/'
],
            'country_code' => 'required',
            'device_token' => 'nullable|string',
            
            'device_id'    => 'nullable|string',
            'device_type'  => 'nullable|in:android,ios,web',
        ];
    }


      public function messages() {
    return [
  'phone.regex' => __('validation.phone_format'),
    ];
  }
}