<?php

namespace App\Http\Resources\Api\Provider;

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
            'id' => $this->id,
            'name' => $this->when(request()->route()->getName() == 'products.show', $this->getTranslations('name'), $this->name),
            'price' => $this->price,
            'quantity' => $this->quantity,
            'description' => $this->when(request()->route()->getName() == 'products.show', $this->getTranslations('description'), $this->description),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'image' => MediaResource::make($this->getFirstMedia('product-images')),
            'images' => $this->when(
                $request->routeIs('products.show'),
                MediaResource::collection($this->getMedia('product-images'))
            ),
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'name' => $this->provider->name,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
        ];
    }
}
