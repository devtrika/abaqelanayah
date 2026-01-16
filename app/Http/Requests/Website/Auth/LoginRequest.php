<?php

namespace App\Http\Requests\Website\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Extract country code and phone from intlTelInput format
        // The intlTelInput sends the full number, we need to split it
        if ($this->has('phone')) {
            $phone = $this->input('phone');
            
            // Remove any non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Default to Saudi Arabia country code if not specified
            $countryCode = '966';
            
            // If phone starts with country code, extract it
            if (strlen($phone) > 10) {
                // Assume first 1-3 digits are country code
                if (str_starts_with($phone, '966')) {
                    $countryCode = '966';
                    $phone = substr($phone, 3);
                } elseif (str_starts_with($phone, '965')) {
                    $countryCode = '965';
                    $phone = substr($phone, 3);
                } elseif (str_starts_with($phone, '971')) {
                    $countryCode = '971';
                    $phone = substr($phone, 3);
                }
            }
            
            // Remove leading zero if present
            $phone = ltrim($phone, '0');
            
            $this->merge([
                'phone' => $phone,
                'country_code' => $countryCode,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|numeric|digits_between:8,10',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.numeric' => 'رقم الجوال يجب أن يحتوي على أرقام فقط',
            'phone.digits_between' => 'رقم الجوال غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ];
    }
}

