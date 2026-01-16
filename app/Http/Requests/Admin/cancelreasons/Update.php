<?php

namespace App\Http\Requests\Admin\cancelreasons;

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
            'reason' => 'required|array',
            'reason.*' => 'required|string|max:500',
        ];
    }
}
