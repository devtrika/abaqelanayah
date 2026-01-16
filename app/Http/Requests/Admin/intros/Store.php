<?php

namespace App\Http\Requests\Admin\intros;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $default_lang = config('app.locale');

        return [
            'title'                     => 'required|array',
            "title.{$default_lang}"     => 'required|string',
            'title.*'                   => 'nullable|string',
            'description'               => 'required|array',
            "description.{$default_lang}" => 'required|string',
            'description.*'             => 'nullable|string',
            'image'                     => ['required','image'],
        ];
    }
}
