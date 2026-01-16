<?php

namespace App\Http\Requests\Admin\images;

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
            'name.*'                  => 'nullable|string|max:191',
            'image_ar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_en' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'nullable|in:0,1',
            'link' => 'nullable|string|max:255',
        ];
    }

  
}
