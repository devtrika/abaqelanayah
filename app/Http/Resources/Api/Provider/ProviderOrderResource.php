<?php

namespace App\Http\Resources\Api\Provider;

use App\Enums\OrderStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order->order_number,
            'user' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'phone' => $this->order->user->phone,
                'country_code' => $this->order->user->country_code,
            ],
            'items' => $this->orderItems->map(function($item) {
                return ['name' => $item->name];
            }),
            'status' => __('admin.' . $this->status),
            'status_enum' => OrderStatus::from($this->status),
            'total' => (float) $this->total,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('d M Y, h:i A'),
        ];
    }
}
