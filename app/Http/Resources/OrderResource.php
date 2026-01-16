<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'order_id'     => $this->id,
            'order_status' => \App\Enums\OrderStatus::getLabel($this->status),
            'order_date'   => $this->created_at?->format('Y-m-d H:i:s'),
            'user_name'    => $this->user?->name,
            'user_phone'   => $this->user?->phone,
            'order_notes'  => $this->notes,
            'address'      => $this->address?->full_name,
            'lat'          => $this->lat,
            'lng'          => $this->lng,
        ];
    }
}
