<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class Adv extends BaseModel
{
    protected const IMAGEPATH = 'advs';

    use HasTranslations;
    protected $fillable = ['title','description' ,'image'];
    public $translatable = ['title','description'];

    /**
     * Get the advertisement image URL with a fallback to default image
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image']) && $this->attributes['image'] && $this->attributes['image'] != 'default.png') {
            return $this->getImage($this->attributes['image'], self::IMAGEPATH);
        }

        return $this->defaultImage(self::IMAGEPATH);
    }
}
