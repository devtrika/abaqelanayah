<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasAutoMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class Brand extends BaseModel implements HasMedia
{
     use HasTranslations , HasAutoMedia;


       protected array $autoMedia = [
        // Single file: request field "image" -> collection "product_image"
        'image'   => 'brands',
        // Multiple files: request field "gallery[]" -> collection "product_gallery"
        'gallery' => ['collection' => 'product_gallery', 'multiple' => true],

    ];

    protected $fillable = ['name','is_active'];
    public $translatable = ['name'];


      public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('brands') ?: asset('storage/images/default.png');
    }

}
