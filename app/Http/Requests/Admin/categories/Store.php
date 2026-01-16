<?php

namespace App\Http\Requests\Admin\categories;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $default_lang = app()->getLocale();
        
        return [
            'name'                    => 'required|array',
            "name.{$default_lang}"    => 'required|string|max:191',
            'name.*'                  => 'required|string|max:191',
            'image'                   => ['nullable','image'],
            'parent_id'               => 'nullable|exists:categories,id',
        ];
    }
}
