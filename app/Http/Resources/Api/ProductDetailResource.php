<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\MediaResource;

class ProductDetailResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'stock' => $this->stock,
            'images' => MediaResource::collection($this->getMedia('product-images'))->toArray($request),
            'is_active' => $this->is_active,
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->name : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'related_products' => ProductResource::collection($this->when(isset($this->related_products), $this->related_products)),
        ];
    }
}
