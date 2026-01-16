<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
public function rules(): array
{
    return [
        'name' => 'nullable|array',
        'name.ar' => 'nullable|string',
        'name.en' => 'nullable|string',
        'description' => 'nullable|array',
        'description.ar' => 'nullable|string',
        'description.en' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'base_price' => 'nullable|numeric|min:0',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'is_active' => 'boolean',

'options' => 'nullable|array|min:1',
'options.*.name' => 'nullable|string|max:191',
'options.*.type' => 'nullable|string|in:weight,cutting,packaging',
'options.*.additional_price' => 'nullable|numeric|min:0',
'options.*.is_default' => 'nullable|boolean',

    ];
}


    public function authorize(): bool
    {
        return true;
    }
}
