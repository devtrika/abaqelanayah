<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class IntroService extends BaseModel
{
    use HasTranslations; 
    protected $fillable = ['title' , 'description'];
    public $translatable = ['title' , 'description'];
}
