<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends BaseModel 
{
    use HasTranslations  , UploadTrait;

    const IMAGEPATH = "paymentmethods";
    public $translatable = ['name'];
    protected $fillable = ['name' , 'is_active', 'image'];

   
}
