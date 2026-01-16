<?php

namespace App\Http\Resources\Api\Delivery;

use App\Enums\OrderStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'delivery_id'   => $this->delivery_id,
            'refund_number' => $this->refund_number,
            'amount'        => (float) $this->refund_amount,
            'status' => __('admin.' . $this->status),

            'status_enum' => OrderStatus::tryFrom($this->status),

            'reason'        => $this->reason,
            'created_at'    => $this->created_at->format('Y/m/d - H:i'),
            'client' => [
                'id' => $this->order?->user_id ?? null,
                'name' => $this->order?->user?->name ?? ($this->order?->recipient_name ?? null),
                'phone' => $this->order?->user?->phone ?? ($this->order?->reciver_phone ?? null),
            ],
            'client_address' => [
                'address_name' => $this->order?->address?->address_name ?? null,
                'latitude' => $this->order?->address?->latitude ?? $this->order?->address_latitude ?? null,
                'longitude' => $this->order?->address?->longitude ?? $this->order?->address_longitude ?? null,
                'city' => $this->order?->address?->city?->name ?? null,
                'district' => $this->order?->address?->district?->name ?? null,
            ],
        ];
    }
}
