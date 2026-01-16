<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Responder;

class BusinessController extends Controller
{
    public function check()
    {
        $isBusinessRegister = false; 

        return Responder::success([
            'is_business_register' => $isBusinessRegister
        ]);
    }
}
