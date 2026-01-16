<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Client\ProductResource;
use App\Http\Resources\Api\Client\ServiceResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        $product = $this->product;

        return [
            'id' => $this->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'image' => $product->image_url,
            'price' => $this->price,
            'discount_amount' => $this->discount_amount,
            'price_after_discount' => $this->price - $this->discount_amount,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'is_refunded' => (bool) ($product->is_refunded ?? false),

        ];
    }
}
