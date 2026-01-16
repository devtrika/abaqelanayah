<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Api\BaseApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyResetCodeRequest extends BaseApiRequest
{
    public function __construct(Request $request) {
        $request['phone']        = fixPhone($request['phone']);
        $request['country_code'] = fixPhone($request['country_code']);
    }

    public function rules() {
    return [
        'country_code' => 'required|exists:users,country_code',
        'phone'        => 'required|exists:users,phone',
        'code'         =>'required|numeric|digits:5',
    ];
    }
}
