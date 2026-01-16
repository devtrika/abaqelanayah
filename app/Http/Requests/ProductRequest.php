<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
public function rules(): array
{
    return [
        'name' => 'required|array',
        'name.ar' => 'required|string',
        'name.en' => 'required|string',
        'description' => 'nullable|array',
        'description.ar' => 'nullable|string',
        'description.en' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'base_price' => 'required|numeric|min:0',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'is_active' => 'boolean',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
'options' => 'required|array|min:1',
'options.*.name' => 'required|string|max:191',
'options.*.type' => 'required|string|in:weight,cutting,packaging',
'options.*.additional_price' => 'nullable|numeric|min:0',
'options.*.is_default' => 'nullable|boolean',

    ];
}


    public function authorize(): bool
    {
        return true;
    }
}
