<?php

namespace App\Models;

class Social extends BaseModel
{
    protected const IMAGEPATH = 'socials';
    protected $fillable = ['link' , 'icon' , 'name'];

    public function getIconAttribute()
    {
        if (isset($this->attributes['icon']) && $this->attributes['icon'] && $this->attributes['icon'] != 'default.png') {
            $image = $this->getImage($this->attributes['icon'], 'socials');
        } else {
            $image = $this->defaultImage(static::IMAGEPATH);
        }
        return $image;
    }

    /**
     * Get the social icon URL with a fallback to default image
     *
     * @return string
     */
    public function getIconUrlAttribute()
    {
        if (isset($this->attributes['icon']) && $this->attributes['icon'] && $this->attributes['icon'] != 'default.png') {
            return $this->getImage($this->attributes['icon'], self::IMAGEPATH);
        }

        return $this->defaultImage(self::IMAGEPATH);
    }

    public function setIconAttribute($value) {
        if (null != $value) {
            isset($this->attributes['icon']) ? $this->deleteFile($this->attributes['icon'] , static::IMAGEPATH) : '';
            $this->attributes['icon'] = $this->uploadAllTyps($value, static::IMAGEPATH);
        }
    }

}
