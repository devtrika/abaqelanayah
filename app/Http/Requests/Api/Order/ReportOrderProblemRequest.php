<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class ReportOrderProblemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust this based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            // Use required_without to ensure at least one is provided.
            // Mutual exclusion (not both) is enforced in withValidator().
            'problem_id' => 'nullable|exists:problems,id|required_without:notes',
            'notes' => 'nullable|string|max:1000|required_without:problem_id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('problem_id') && $this->filled('notes')) {
                $validator->errors()->add('problem_id', __('apis.problem_and_notes_mutually_exclusive') ?: 'You must provide either problem_id or notes, not both.');
            }
        });
    }

}
