<?php

namespace App\Http\Requests\Admin\products;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $default_lang = config('app.locale');

        // If the authenticated admin is a branch manager, relax validation:
        // we only allow branch-level quantity edits in the controller and skip all other field validations here.
        // $admin = auth()->guard('admin')->user();
        // if ($admin && (int) $admin->role_id === 2) {
        //     return [
        //         // Optional: allow qty and branch_id to pass through if provided; controller will validate strictly
        //         'quantity' => 'nullable|integer|min:0',
        //         // 'branch_id' => 'nullable|integer|exists:branches,id',
        //     ];
        // }

        return [
            'name'                  => 'required|array',
            'name.*'                => 'nullable|string|max:191',
            'description'           => 'nullable|array',
            'description.*'         => 'nullable|string',
            'parent_category_id'    => 'required|exists:categories,id',
            'category_id'           => 'required|exists:categories,id',
            'base_price'            => 'required|numeric|min:0',
            'discount_percentage'   => 'nullable|numeric|min:0|max:100',
            'quantity'              => 'required|integer|min:0',
            'images'                => 'nullable|array',
            'images.*'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'is_active'             => 'nullable|boolean',
            'is_refunded'           => 'required|boolean',
            'brand_id'              => 'required|exists:brands,id',
        ];
    }

    public function messages()
    {
        $ar = app()->getLocale() == 'ar';
        return [
            'options.*.name.required' => $ar ? 'هذا الحقل مطلوب.' : 'The option name is required.',
            'options.*.name.string' => $ar ? 'يجب أن يكون اسم الخيار نصاً.' : 'The option name must be a string.',
            'options.*.name.max' => $ar ? 'اسم الخيار يجب ألا يزيد عن 191 حرفاً.' : 'The option name may not be greater than 191 characters.',
            'options.*.type.required' => $ar ? 'نوع الخيار مطلوب.' : 'The option type is required.',
            'options.*.type.in' => $ar ? 'نوع الخيار يجب أن يكون: وزن، تقطيع، تغليف.' : 'The option type must be one of: weight, cutting, packaging.',
            'options.*.additional_price.numeric' => $ar ? 'سعر الإضافة يجب أن يكون رقم.' : 'The additional price must be a number.',
            'options.*.additional_price.min' => $ar ? 'سعر الإضافة يجب أن يكون على الأقل 0.' : 'The additional price must be at least 0.',
            'options.*.is_default.boolean' => $ar ? 'قيمة الخيار الافتراضي يجب أن تكون نعم أو لا.' : 'The is default field must be true or false.',
        ];
    }


}
