<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadTrait;

 class BaseModel extends Model
{
    use UploadTrait;

    public function scopeSearch($query, $searchArray = [])
{
    $searchArray = is_array($searchArray) ? $searchArray : [];

    $query->where(function ($q) use ($searchArray) {
        foreach ($searchArray as $key => $value) {
            if (is_null($value) || $value === '') continue;

            if (str_contains($key, '_id')) {
                $q->where($key, $value);
            } elseif ($key === 'created_at_min') {
                $q->whereDate('created_at', '>=', $value);
            } elseif ($key === 'created_at_max') {
                $q->whereDate('created_at', '<=', $value);
            } elseif ($key !== 'order') {
                $q->where($key, 'like', "%{$value}%");
            }
        }
    });

    $orderDirection = isset($searchArray['order']) ? $searchArray['order'] : 'DESC';

    return $query->orderBy('created_at', $orderDirection);
}


    public function getImageAttribute() {
        return isset($this->attributes['image'])
            ? $this->getImage($this->attributes['image'], static::IMAGEPATH)
            : $this->defaultImage(static::IMAGEPATH);
    }

    public function setImageAttribute($value) {
        if (null != $value) {
            try {
                // Log what we're receiving
                \Illuminate\Support\Facades\Log::info('setImageAttribute called', [
                    'value_type' => gettype($value),
                    'is_object' => is_object($value),
                    'class' => is_object($value) ? get_class($value) : 'not an object',
                    'model' => get_class($this),
                    'image_path' => static::IMAGEPATH ?? 'undefined'
                ]);

                // Delete old image if exists
                if (isset($this->attributes['image']) && $this->attributes['image'] != 'default.png') {
                    $this->deleteFile($this->attributes['image'], static::IMAGEPATH);
                }

                // Upload the new image
                $this->attributes['image'] = $this->uploadAllTyps($value, static::IMAGEPATH);

                // Log the result
                \Illuminate\Support\Facades\Log::info('Image uploaded successfully', [
                    'new_image' => $this->attributes['image']
                ]);
            } catch (\Exception $e) {
                // Log any errors
                \Illuminate\Support\Facades\Log::error('Error in setImageAttribute: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);

                // Set default image on error
                $this->attributes['image'] = 'default.png';
            }
        }
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }


    public static function boot() {
        parent::boot();
        /* creating, created, updating, updated, deleting, deleted, forceDeleted, restored */

        static::deleted(function($model) {
            if (isset($model->attributes['image'])) {
                $model->deleteFile($model->attributes['image'], static::IMAGEPATH );
            }
        });

    }

}