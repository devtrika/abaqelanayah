<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class RefundReason extends BaseModel
{
     use HasTranslations;

    protected $translatable = ['reason'];

    protected $fillable = [
        'reason'
    ];
}



