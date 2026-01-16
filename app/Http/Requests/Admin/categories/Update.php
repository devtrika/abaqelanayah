<?php

namespace App\Http\Requests\Admin\categories;

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
        $default_lang = app()->getLocale();

        return [
            'name'                    => 'required|array',
            "name.{$default_lang}"    => 'required|string|max:191',
            'name.*'                  => 'nullable|string|max:191',
            'parent_id'               => 'nullable|exists:categories,id',
            'image'                   => ['nullable','image'],
        ];
    }
}
