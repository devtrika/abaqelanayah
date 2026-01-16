<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRateResource extends JsonResource
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
            'timing_rate' => $this->timing_rate,
            'quality_rate' => $this->quality_rate,
            'service_rate' => $this->service_rate,
           
            'body' => $this->body,
            'images' => MediaResource::collection($this->getMedia('order_rates')),
            'videos' => MediaResource::collection($this->getMedia('order_rate_videos')),
        ];
    }
}
