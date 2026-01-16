<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\Model;

class District extends BaseModel
{
    use HasTranslations;

    protected $fillable = ['name', 'status', 'city_id'];
    public $translatable = ['name'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
}
