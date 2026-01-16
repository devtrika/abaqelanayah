<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRefundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'refund_reason_id' => 'nullable|exists:refund_reasons,id|required_without:notes',
            'notes' => 'nullable|string|max:1000|required_without:refund_reason_id',
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:products,id',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv,flv,webm|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.required' => __('validation.required', ['attribute' => __('admin.order')]),
            'order_id.exists' => __('validation.exists', ['attribute' => __('admin.order')]),
            'refund_reason_id.required' => __('validation.required', ['attribute' => __('admin.refund_reason')]),
            'refund_reason_id.exists' => __('validation.exists', ['attribute' => __('admin.refund_reason')]),
            'items.required' => __('validation.required', ['attribute' => __('admin.items')]),
            'items.min' => __('validation.min.array', ['attribute' => __('admin.items'), 'min' => 1]),
            'items.*.required' => __('validation.required', ['attribute' => __('admin.product')]),
            'items.*.exists' => __('validation.exists', ['attribute' => __('admin.product')]),
        ];
    }
}
