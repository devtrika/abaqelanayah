<?php

namespace App\Http\Requests\Admin\delivery;

use Illuminate\Validation\Rule;
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
                        'gender' => 'required|in:male,female',
            'district_id' => 'required|exists:districts,id',
            // 'region_id' => 'required|exists:regions,id',
            'phone'    => [
                'required',
                'phone:SA',
                Rule::unique('users', 'phone')->whereNull('deleted_at'),
            ],
            'email'    => 'nullable|email|max:191|unique:users,email,NULL,id,deleted_at,NULL',
                        'password' => 'required|confirmed|min:6|max:191',
            'type' => 'sometimes',
            'image'   => ['nullable', 'image'],
        ];
    }

    public function messages() {
        return [
          'phone.phone' => __('validation.phone_format'),
                    'password.confirmed' => __('validation.confirmed'),
        ];
      }
}
