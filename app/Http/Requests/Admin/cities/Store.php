<?php

namespace App\Http\Requests\Admin\cities;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name'      => ['required', 'array', function($attribute, $value, $fail) {
                if (!collect($value)->filter()->count()) {
                    $fail(__('admin.at_least_one_name_required'));
                }
            }],
            'name.*'    => 'nullable|string|max:191',
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'smoetmies|exists:countries,id',

        ];
    }
}
