<?php

namespace App\Http\Resources\Api\Settings;

use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = app()->getLocale();
        $imageKey = $locale === 'ar' ? 'image_ar' : 'image_en';
        return [
            'id'         => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'type' => $this->type,

            // Main image for current locale
            'image' => MediaResource::make($this->getMedia($imageKey)->first()),

            // For backward compatibility
           ];
    }
}
