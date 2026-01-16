<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest {
  public function authorize() {
    return true;
  }

  public function rules() {
    return [
      'name'         => 'required|max:191',
      'country_code' => 'required|numeric|digits_between:2,5',
      'phone'        => 'required|min:8|phone:SA|unique:admins,phone',
      'email'        => 'required|email|max:191|unique:admins,email',
      'password'     => 'required|min:6|max:255',
      'avatar'       => 'nullable|image',
      'role_id'      => 'required|exists:roles,id',
      // 'is_blocked'  => 'required|in:0,1',
    ];
  }
  public function messages() {
    return [
      'phone.phone' => __('validation.phone_format'),
    ];
  }

}
