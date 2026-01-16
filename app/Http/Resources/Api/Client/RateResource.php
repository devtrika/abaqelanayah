<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rate' => $this->rate,
            'body' => $this->body,
            'rateable_type' => $this->rateable_type,
            'rateable_id' => $this->rateable_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'image' => $this->user->image,
            ],
            'rateable' => $this->when($this->relationLoaded('rateable'), function () {
                // Return different data based on rateable type
                if ($this->rateable_type === 'App\\Models\\Provider') {
                    return [
                        'id' => $this->rateable->id,
                        'commercial_name' => $this->rateable->commercial_name,
                        'type' => 'provider'
                    ];
                } elseif ($this->rateable_type === 'App\\Models\\Product') {
                    return [
                        'id' => $this->rateable->id,
                        'name' => $this->rateable->name,
                        'type' => 'product'
                    ];
                } elseif ($this->rateable_type === 'App\\Models\\Service') {
                    return [
                        'id' => $this->rateable->id,
                        'name' => $this->rateable->name,
                        'type' => 'service'
                    ];
                }

                return null;
            }),
            'media' => $this->when($this->relationLoaded('media') || $this->media_urls, $this->media_urls),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
