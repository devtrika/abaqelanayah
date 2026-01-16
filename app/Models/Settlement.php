<?php

namespace App\Models;

class Settlement extends BaseModel
{
    protected const IMAGEPATH = 'settlements';

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'amount' ,
        'status' ,
        'image'
    ];

    public function transactionable() {
        //? rel with users, admins, providers, delegates
        return $this->morphTo();
    }

    public function getImagePathAttribute() {
        if (isset($this->attributes['image']) && $this->attributes['image']) {
            return $this->getImage($this->attributes['image'], self::IMAGEPATH);
        }

        return $this->defaultImage(self::IMAGEPATH);
    }

    /**
     * Get the settlement image URL with a fallback to default image
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

    public static function boot() {
        parent::boot();
        /* creating, created, updating, updated, deleting, deleted, forceDeleted, restored */

        static::deleted(function($model) {
            $model->deleteFile($model->attributes['image'], 'settlements');
        });

    }

}
