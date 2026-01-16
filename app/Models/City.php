<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class City extends BaseModel
{
    use HasTranslations; 
    
    protected $fillable = ['name','region_id','country_id'];
    
    public $translatable = ['name'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'city_id', 'id');
    }

    public function getUsersCountAttribute()
    {
        return $this->users()->where('type', 'client')->count();
    }
    

    public function getProvidersCountAttribute()
    {
        return $this->users()->where('type', 'provider')->count();
    }
    

}
