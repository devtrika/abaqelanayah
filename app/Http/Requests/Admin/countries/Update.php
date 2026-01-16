<?php

namespace App\Http\Requests\Admin\countries;

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
        $default_lang = config('app.locale');

        return [
            'name'                   => 'required|array',
            "name.{$default_lang}"   => 'required|string|max:191',
            'name.*'                 => 'nullable|string|max:191',
            'currency'               => 'required|array',
            "currency.{$default_lang}" => 'required|string|max:191',
            'currency.*'             => 'nullable|string|max:191',
            'key'                    => 'required|unique:countries,key,'.$this->id,
            'currency_code'          => 'required|unique:countries,currency_code,'.$this->id,
            'flag'                   => 'nullable',
        ];
    }
}
