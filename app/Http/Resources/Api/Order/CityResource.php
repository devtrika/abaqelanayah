<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'region_id' => $this->region_id,
            'region' => $this->whenLoaded('region', function() {
                return [
                    'id' => $this->region->id,
                    'name' => $this->region->name,
                    'country_id' => $this->region->country_id,
                ];
            }),
            'is_active' => (bool) $this->is_active,
        ];
    }
}
