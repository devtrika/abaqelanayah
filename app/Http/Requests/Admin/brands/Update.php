<?php

namespace App\Http\Requests\Admin\brands;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            // Image optional when updating
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        foreach (languages() as $lang) {
            $rules["name.$lang"] = 'required|string|max:191';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        foreach (languages() as $lang) {
            $messages["name.$lang.required"] = __('admin.name') . " ($lang) " . __('admin.this_field_is_required');
        }

        return $messages;
    }
}
