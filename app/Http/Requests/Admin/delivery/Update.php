<?php

namespace App\Http\Requests\Admin\delivery;

use Illuminate\Validation\Rule;
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
    $id = $this->id ?? $this->user ?? null;

        return [
            'name'     => 'required|min:6|max:60',
            // 'is_blocked'  => 'required',
            'country_code' => 'required',
            'city_id' => 'required|exists:cities,id',
                        'gender' => 'required|in:male,female',
            // 'region_id' => 'required|exists:regions,id',
            'phone'    => [
                'required',
                'phone:SA',
                Rule::unique('users', 'phone')->whereNull('deleted_at')->ignore($id),
            ],
            'email'    => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($id),
            ],
            'password' => ['nullable', 'confirmed', 'min:6', 'max:191'],
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
