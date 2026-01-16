<?php
namespace App\Http\Resources\Api\Client;

use App\Http\Resources\Api\Client\ProductResource;
use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->commercial_name,
            'provider_type'          => $this->salon_type,
            'city'                   => optional(optional($this->user)->city)->name,
            'views' => $this->views_count,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'distance_km'            => $this->distance_from_user,

            'is_currently_available' => $this->is_currently_available,
            'is_favorite' => $this->is_favorite,

            'rate'                   => $this->rate_summary,
            'description'            => $this->description,
            'salon_images'           => MediaResource::collection($this->getMedia('salon_images')),
            'working_hours'          => WorkingHoursResource::collection($this->whenLoaded('WorkingHours')),
            'services'               => ServiceResource::collection($this->whenLoaded('activeServices')),
            'products'               => ProductResource::collection($this->whenLoaded('activeProducts')),
            'rates'                  => RateResource::collection($this->whenLoaded('rates', function() {
                return $this->rates->where('status', 'approved');
            })),

        ];
    }
}
