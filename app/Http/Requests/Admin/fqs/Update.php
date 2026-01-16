<?php

namespace App\Http\Requests\Admin\fqs;

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
            'question.*'                => 'required',
            'answer.*'                  => 'required',
            'audience_type'             => 'required|in:client,delivery',
        ];
    }
}
