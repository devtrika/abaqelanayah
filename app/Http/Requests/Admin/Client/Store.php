<?php

namespace App\Http\Requests\Admin\Client;

use App\Rules\ClientPhoneUnique;
use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'name'     => 'required|min:3|max:60',
            'is_active'  => 'nullable',
            'country_code' => 'required',
            'city_id' => 'required|exists:cities,id',
            // 'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'phone'    => [
                'required',
                new ClientPhoneUnique()
            ],
            'gender' => 'required|in:male,female',
            'password' => 'required|confirmed|min:6|max:191',
            'email'    => 'nullable|email|max:191|unique:users,email,NULL,id,deleted_at,NULL',
            'type' => 'sometimes',
            'image'   => ['nullable', 'image'],
        ];
    }

    public function messages() {
        return [
          'phone.phone' => __('validation.phone_format'),
        ];
      }
}
