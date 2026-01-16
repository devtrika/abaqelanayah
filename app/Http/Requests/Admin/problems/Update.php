<?php

namespace App\Http\Requests\Admin\problems;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'problem'                  => 'required|max:191',
           
        ];
    }
}
