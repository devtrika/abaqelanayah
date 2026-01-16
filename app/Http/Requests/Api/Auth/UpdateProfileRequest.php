<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Http\Request;

class UpdateProfileRequest extends BaseApiRequest {
  public function __construct(Request $request) {
    if($request['phone']){
      $request['phone']        = fixPhone($request['phone']);
    }
    if($request['country_code']){
      $request['country_code'] = fixPhone($request['country_code']);
    }

    if ($request['phone'] && auth()->user()->phone !== $request['phone']) {
      $request['active'] = false;
    }

  }

  public function rules() {
    return [
      'country_code' => 'sometimes|required|numeric|digits_between:2,5',
      'phone'        => 'sometimes|required|numeric|digits_between:8,10|unique:users,phone,' . auth()->id(),
      'name'         => 'sometimes|required|max:50|unique:users,name,' . auth()->id(),
      'email'        => 'sometimes|nullable|email|max:50|unique:users,email,' . auth()->id(),
      'active'       => '',
      'city_id'      => 'sometimes|required|exists:cities,id',
      ];
  }

  public function withValidator($validator) {
    $validator->after(function ($validator) {
      // if ($this->has('old_password') && !Hash::check($this->old_password, auth()->user()->password)) {
      //   $validator->errors()->add('old_password', trans('auth.incorrect_old_pass'));
      // }
    });
  }

}
