<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class Fqs extends BaseModel
{
    use HasTranslations;
    protected $fillable = ['question','answer' , 'audience_type'];
    public $translatable = ['question','answer'];
    
}
