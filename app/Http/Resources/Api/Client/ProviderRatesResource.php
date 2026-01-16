<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\MediaResource;

class ProviderRatesResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name'=> $this->user->name,
                'image' => $this->user->image,
            ],
            'media' => MediaResource::collection($this->getMedia('rates-images'))
        ];
    }
}
