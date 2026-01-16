<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'image' => $this->image_url,
            'is_active' => $this->is_active,
            'parent_id' => $this->when($this->parent_id, $this->parent_id),
            'parent_name' => $this->when($this->parent_id, $this->parent->name ?? ''),
            'products' => ProductIndexResource::collection($this->whenLoaded('products')),
            // 'children' => CategoryWithProductsResource::collection($this->whenLoaded('children')),
        ];
    }
}
