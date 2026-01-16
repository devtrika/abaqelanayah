<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;  
    }

    public function rules()
    {
        $isCreate = $this->isMethod('post');
        $requiredRules = $isCreate ? ['required'] : ['sometimes', 'required'];
        $requiredStr = implode('|', $requiredRules);

        return [
            'phone' => $requiredStr . '|phone:SA',
            'description' => 'nullable|string|max:255',
            'address_name' => array_merge($requiredRules, [
                'min:3',
                'regex:/[\p{L}]/u', // must contain at least one letter
                'regex:/^[\p{L}\p{N} ]+$/u', // only letters, numbers, spaces
            ]),
            'recipient_name' => array_merge($requiredRules, [
                'min:3',
                'regex:/[\p{L}]/u', // must contain at least one letter
                'regex:/^[\p{L}\p{N} ]+$/u', // only letters, numbers, spaces
            ]),
            'city_id' => 'nullable|exists:cities,id',
            'districts_id' => 'nullable|exists:districts,id',
            'country_code' => $requiredStr . '|numeric|digits_between:2,5',
            'latitude' => $requiredStr,
            'longitude' => $requiredStr,
            'is_default' => 'nullable|boolean',
        ];
    }
}
