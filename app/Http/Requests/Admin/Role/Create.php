<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'name'                  => 'required|array',
            "name.{$default_lang}"  => 'required|string|max:191',
            'name.*'                => 'nullable|string|max:191',
            'permissions'           => 'nullable|array'
        ];
    }
}
