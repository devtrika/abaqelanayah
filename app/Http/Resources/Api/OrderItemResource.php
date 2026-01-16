<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'title' => $this->product->title,
            'description' => $this->product->description,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'total' => $this->total,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
