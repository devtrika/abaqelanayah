<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => (float) $this->price,
            'discount_price' => (float) $this->discount_price,
            'stock' => $this->stock,
            'image' => $this->image,
            'is_active' => (bool) $this->is_active,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'title' => $this->category->title,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
