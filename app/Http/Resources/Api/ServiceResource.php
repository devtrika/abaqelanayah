<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Service Resource for provider services
 * 
 * Response structure:
 * {
 *   "id": 1,
 *   "name": "Service Name",
 *   "price": "100.00",
 *   "duration": 60,
 *   "formatted_duration": "1h",
 *   "expected_time_to_accept": 30,
 *   "formatted_expected_time": "30m",
 *   "description": "Service description",
 *   "is_active": true,
 *   "provider": { ProviderDataResource },
 *   "category": { CategoryResource },
 *   "created_at": "2024-01-01T00:00:00.000000Z",
 *   "updated_at": "2024-01-01T00:00:00.000000Z"
 * }
 */
class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->when(request()->route()->getName() == 'services.show', $this->getTranslations('name'), $this->name),
            'price' => $this->price,
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'expected_time_to_accept' => $this->expected_time_to_accept,
            'formatted_expected_time' => $this->formatted_expected_time,
            'description' => $this->when(request()->route()->getName() == 'services.show', $this->getTranslations('description'), $this->description),
            'is_active' => $this->is_active,
            
            // Relationships
            'provider' => $this->whenLoaded('provider', function() {
                return new ProviderDataResource($this->provider);
            }),
            
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'is_active' => $this->category->is_active,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
