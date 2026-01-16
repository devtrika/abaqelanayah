<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
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
            'name' => $this->commercial_name,
            'city' => $this->user?->city?->name,
            'rate' =>$this->rate_summary,
            'is_favorite' => $this->is_favorite,
            'image' => $this->logo_url,
            'views' => $this->views_count,
            'is_currently_available' => $this->is_currently_available,
            'distance_km' => $this->distance_from_user,
        ];
    }
}
