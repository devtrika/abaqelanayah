<?php

namespace App\Http\Requests\Admin\countries;

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
            'name'                   => 'required|array',
            "name.{$default_lang}"   => 'required|string|max:191',
            'name.*'                 => 'nullable|string|max:191',
            'currency'               => 'required|array',
            "currency.{$default_lang}" => 'required|string|max:191',
            'currency.*'             => 'nullable|string|max:191',
            'key'                    => 'required|unique:countries,key',
            'currency_code'          => 'required|unique:countries,currency_code',
            'flag'                   => 'nullable',
        ];
    }
}
