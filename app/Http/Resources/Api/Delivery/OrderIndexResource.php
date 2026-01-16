<?php

namespace App\Http\Resources\Api\Delivery;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'created_at'   => $this->created_at->format('Y/m/d - H:i'),
            'client_name'  => $this->user?->name, 
            'client_phone' => $this->user?->phone ?? \App\Models\User::where('id', $this->user_id)->value('phone'),
            'total_amount' => (float) $this->total, 
            'address' => $this->when(true, function() {
                // For gift orders we return the gift recipient/location fields instead
                // of the regular address object.
                if ($this->order_type === 'gift') {
                    return [
                        'reciver_phone' => $this->reciver_phone ?? null,
                        'gift_address_name' => $this->gift_address_name ?? null,
                        'gift_latitude' => isset($this->gift_latitude) ? (float) $this->gift_latitude : null,
                        'gift_longitude' => isset($this->gift_longitude) ? (float) $this->gift_longitude : null,
                    ];
                }

                // Fallback: return stored address when available
                if ($this->address || $this->address_id) {
                    $addr = $this->address ?? \App\Models\Address::find($this->address_id);
                    if (!$addr) return null;

                    return [
                        'id' => $addr->id,
                        'address_name' => $addr->address_name,
                        'recipient_name' => $addr->recipient_name,
                        'phone' => $addr->phone,
                        'country_code' => $addr->country_code ?? null,
                        'city' => $addr->city?->name ?? null,
                        'district' => $addr->district?->name ?? null,
                        'latitude' => $addr->latitude,
                        'longitude' => $addr->longitude,
                        'full' => trim(($addr->address_name ?? '') . ' ' . ($addr->description ?? '')),
                    ];
                }

                return null;
            }),
            'status'       => [
                'key'   => $this->status,
                'label' => \App\Enums\OrderStatus::tryFrom($this->status)?->label() ?? $this->status,
                'color' => \App\Enums\OrderStatus::tryFrom($this->status)?->color() ?? null,
            ],
        ];
    }
}
