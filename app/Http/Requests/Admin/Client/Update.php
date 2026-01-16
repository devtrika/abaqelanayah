<?php

namespace App\Http\Requests\Admin\Client;

use App\Rules\ClientPhoneUnique;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            // 'is_blocked' => 'required|in:active,suspended,banned,deleted',
            'country_code' => 'required',
            'city_id' => 'required|exists:cities,id',
            // 'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'phone'    => [
                'required',
                new ClientPhoneUnique($this->id)
            ],
                        'gender' => 'required|in:male,female',
            'email'    => 'nullable|email|max:191|unique:users,email,'.$this->id.',id,deleted_at,NULL',
            'password' => ['nullable', 'min:6'],
            'image'   => ['nullable', 'image'],
        ];
    }
}
