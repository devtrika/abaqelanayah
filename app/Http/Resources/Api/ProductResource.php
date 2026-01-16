<?php
namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'images'               => $this->getMedia('product-images')->pluck('original_url'),
            'description'          => $this->description,
            'category_id'          => $this->category_id,
            'category_name'        => $this->category?->name,
            'parent_category_id'   => $this->parent_category_id,
            'parent_category_name' => $this->parentCategory?->name,
            'brand_id' =>$this->brand_id,
            'brand_name' => $this->brand?->name,
             'brand_image' => $this->brand?->image ?? null,
            'base_price'           => $this->base_price,
            'discount_percentage'  => $this->discount_percentage,
            'quantity'             => $this->quantity,
            'final_price'          => $this->base_price - ($this->base_price * ($this->discount_percentage / 100)),
            'in_cart'              => $this->in_cart,
            'is_favourite'         => $this->is_favourite,
            'is_refunded'          => (bool) $this->is_refunded,
            'is_available'          => (bool) $this->is_available,


            // 'options' => ProductOptionResource::collectionGroupedByType($this->whenLoaded('options')),
            'related_products'     => $this->when($this->relationLoaded('category'), function () {
                return ProductIndexResource::collection(
                    $this->category->products()
                        ->where('id', '!=', $this->id)
                        ->where('is_active', true)
                        ->limit(5)
                        ->get()
                );
            }),
            'created_at'           => $this->created_at->toDateTimeString(),
        ];
    }
}
