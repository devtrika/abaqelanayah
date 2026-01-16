<?php

namespace App\Http\Requests\Api;

use App\Facades\Responder;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseApiRequest extends FormRequest {
  use ResponseTrait;
  public function authorize() {
    return true;
  }

  protected function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      Responder::error(
        $validator->errors()->first(),
        $validator->errors()->toArray()
      )
    );
  }
}
