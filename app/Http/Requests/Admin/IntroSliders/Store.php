<?php

namespace App\Http\Requests\Admin\IntroSliders;

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
        $default_lang = config('app.locale');

        return [
            'image'                     => 'required|image',
            'title'                     => 'required|array',
            "title.{$default_lang}"     => 'required|string',
            'title.*'                   => 'nullable|string',
            'description'               => 'required|array',
            "description.{$default_lang}" => 'required|string',
            'description.*'             => 'nullable|string',
        ];
    }
}
