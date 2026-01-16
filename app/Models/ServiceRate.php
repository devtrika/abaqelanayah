<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceRate extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    public $fillabe = ['user_id' , 'service_id' , 'rate' , 'body'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

}
