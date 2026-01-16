<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWalletRechargeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:10',
                'max:10000',
            ],
            'gateway' => [
                'nullable',
                'string',
                'in:visa_master,mada'
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'amount.required' => __('site.amount_required'),
            'amount.numeric'  => __('validation.numeric', ['attribute' => __('site.recharge_amount')]),
            'amount.min'      => __('site.min_amount_10'),
            'amount.max'      => __('site.max_amount_10000'),
            'gateway.in'      => __('validation.in', ['attribute' => __('site.payment_method')]),
        ];
    }
}
