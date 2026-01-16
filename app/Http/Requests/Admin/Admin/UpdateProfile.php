<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name'      => 'required|max:191',
            'phone'     => [
                'required',
//                'phone:SA',
                'unique:admins,phone,' . auth('admin')->id(),
            ],
            'email'     => "required|email|max:191|unique:admins,email,".auth('admin')->id(),
            'avatar'    => 'nullable|image',
        ];
    }

    public function messages() {
        return [
          'phone.phone' => __('validation.phone_format'),
        ];
      }
    
}
