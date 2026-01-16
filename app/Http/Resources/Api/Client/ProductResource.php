<?php
namespace App\Http\Resources\Api\Client;

use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'price'         => $this->price,
            'description'   => $this->description,
            'is_favorite'   => $this->is_favorite,
            'rate'          => $this->rate_summary,
            'provider_name' => $this->provider->commercial_name,
            'in_cart' => $this->in_cart,
            'cart_qunatity' => $this->cart_quantity,
            'available_quantity' => $this->available_quantity,
            'is_refunded'    => (bool) $this->is_refunded,
            'rates'         => RateResource::collection($this->whenLoaded('rates', function() {
                return $this->rates->where('status', 'approved');
            })),    
            'images'        => $this->when(
                request()->routeIs('products.show'),
                fn() => MediaResource::collection($this->getMedia('product-images'))
            ),
            'image'         =>  MediaResource::make($this->getFirstMedia('product-images')),
        ];
    }
}
