<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Problem extends BaseModel
{
    use HasTranslations;
    protected $translatable = ['problem'];
    protected $fillable = ['problem'];
}
