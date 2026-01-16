<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Settings\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductsResource extends JsonResource
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
        'category_name' => $this->category_name,
        'products' => ProductResource::collection($this->products),
    ];
}

}
