<?php

namespace App\Http\Requests\Admin\images;

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
        return [
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'image_ar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_en' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'nullable|in:0,1',
            'link' => 'nullable|string|max:255',
        ];
    }
}
