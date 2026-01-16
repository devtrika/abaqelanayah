<?php

namespace App\Http\Requests\Api\Auth\Client;

use App\Http\Requests\Api\BaseApiRequest;
use App\Rules\ClientPhoneUnique;
use Illuminate\Http\Request;

class UpdateProfileRequest extends BaseApiRequest {
  
  public function __construct(Request $request) {
    if (isset($request['phone'])) {
      $request['phone'] = fixPhone($request['phone']);
    }
    if (isset($request['country_code'])) {
      $request['country_code'] = fixPhone($request['country_code']);
    }
  }

  public function rules() {
    return [
      'name'         => 'sometimes|required|min:3|max:30',
      'email'        => 'sometimes|required|email:rfc,dns|max:50|unique:users,email,' . auth()->id() . ',id,deleted_at,NULL',
      'image'        => 'sometimes|nullable|image',
      //   'phone'        => [
      //   'sometimes',
      //   new ClientPhoneUnique()
      // ],
      'city_id'      => 'sometimes|required|exists:cities,id',
      'region_id'      => 'sometimes|required|exists:regions,id',
      'district_id'    => 'sometimes|required|exists:districts,id',
      'gender' => 'sometimes|nullable|in:male,female',
    ];
  }
}