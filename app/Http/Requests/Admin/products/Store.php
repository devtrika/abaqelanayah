<?php

namespace App\Http\Requests\Admin\products;

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
            'name'                  => 'required|array',
            'name.*'                => 'required|string|min:1|max:191',
            'description'           => 'required|array',
            'description.*'         => 'required|string',
            'parent_category_id'    => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',



            'category_id'           => 'required|exists:categories,id',
            'base_price'            => 'required|numeric|min:1',
            'discount_percentage'   => 'nullable|numeric|min:0|max:100',
            'images'                => 'nullable|array',
            'images.*'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,webp',
            'is_active'             => 'nullable|boolean',
            'is_refunded'           => 'required|boolean',

            // 'options'               => 'required|array',
            // 'options.*.name'        => 'required|string|max:191',
            // 'options.*.type'        => 'required|in:weight,cutting,packaging',
            // 'options.*.additional_price' => 'nullable|numeric|min:0',
            // 'options.*.is_default'  => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'options.required' => __('admin.options_required'),
            'options.*.name.required' => __('admin.option_name_required'),
            'options.*.name.string' => __('admin.option_name_string'),
            'options.*.name.max' => __('admin.option_name_max'),
            'options.*.type.required' => __('admin.option_type_required'),
            'options.*.type.in' => __('admin.option_type_in'),
            'options.*.additional_price.numeric' => __('admin.option_additional_price_numeric'),
            'options.*.additional_price.min' => __('admin.option_additional_price_min'),
            'options.*.is_default.boolean' => __('admin.option_is_default_boolean'),
        ];
    }
    
 
}
