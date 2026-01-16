<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateWithdrawRequest extends FormRequest
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
        $user = auth()->user();

        // Align with UI: withdrawable = current wallet balance minus pending withdraw requests
        $withdrawableBalance = 0;
        if ($user) {
            $balance = $user->wallet_balance;
            $pendingWithdrawSum = \App\Models\Transaction::where('user_id', $user->id)
                ->where('type', 'wallet-withdraw')
                ->where('status', 'pending')
                ->sum('amount');
            $withdrawableBalance = max($balance - $pendingWithdrawSum, 0);
        }
        return [
            'amount' => [
                'required',
                'numeric',
                'min:10',
                'max:' . $withdrawableBalance,
            ],
            'bank_name'           => ['required', 'string', 'max:255'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_number'      => [
                'required_without:iban',
                'digits_between:10,18', // Saudi bank accounts usually 10-18 digits
                'regex:/^[0-9]+$/',    // Only digits
            ],
            'iban'                => [
                'required_without:account_number',
                'string',
                'regex:/^SA[0-9]{2}[A-Z0-9]{20}$/', // Saudi IBAN format (24 chars, no spaces or hyphens)
            ],
            'transfer_reference'  => ['nullable', 'string', 'max:255'],
            'note' => 'nullable|string|max:255'
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        $user = auth()->user();

        // Use same logic as rules() for max amount message
        $withdrawableBalance = 0;
        if ($user) {
            $balance = $user->wallet_balance;
            $pendingWithdrawSum = \App\Models\Transaction::where('user_id', $user->id)
                ->where('type', 'wallet-withdraw')
                ->where('status', 'pending')
                ->sum('amount');
            $withdrawableBalance = max($balance - $pendingWithdrawSum, 0);
        }

        return [
            'amount.required' => __('site.amount_required'),
            'amount.numeric' => __('validation.numeric', ['attribute' => __('site.amount')]),
            'amount.min' => __('site.min_withdraw_amount'),
            'amount.max' => __('site.max_withdraw_amount', ['amount' => number_format($withdrawableBalance, 2)]),
            'bank_name.required' => __('site.bank_name_required'),
            'account_holder_name.required' => __('site.account_holder_name_required'),
            'account_number.required_without' => __('site.account_or_iban_required'),
            'account_number.digits_between' => __('site.account_number_digits'),
            'account_number.regex' => __('validation.regex', ['attribute' => __('site.account_number')]),
            'iban.required_without' => __('site.iban_or_account_required'),
            'iban.regex' => __('site.iban_format_error'),
        ];
    }
}
