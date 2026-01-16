<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Responder extends Facade
{
    public static function getFacadeAccessor()
    {
        return \App\Services\Responder::class;
    }
}
