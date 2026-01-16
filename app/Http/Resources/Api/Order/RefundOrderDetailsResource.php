<?php

namespace App\Http\Resources\Api\Order;

use App\Enums\OrderStatus;
use App\Http\Resources\Api\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundOrderDetailsResource extends JsonResource
{
    /**
     * Transform the refund order resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Get only items requested for refund
        $refundItems = $this->items->where('request_refund', true);
        
        return [
            // Refund order information
            'refund_info' => [
                'refund_number' => $this->refund_number,
                'order_number' => $this->order_number,
                'created_at' => $this->refund_requested_at 
                    ? $this->refund_requested_at->format('Y/m/d - H:i') 
                    : $this->created_at->format('Y/m/d - H:i'),
               'status_enum' => OrderStatus::tryFrom($this->refund_status),
            'status' => __('admin.' . $this->refund_status),

                'customer_name' => $this->user?->name,
                'refund_reason' => (function () {
                    if (!empty($this->notes)) {
                        return $this->notes;
                    }
                    if ($this->refund_reason_id) {
                        $rr = $this->whenLoaded('refundReason') ? $this->refundReason : (\App\Models\RefundReason::find($this->refund_reason_id));
                        return $rr?->reason ?? null;
                    }
                    return null;
                })(),
                'refund_amount' => (float) $this->refund_amount,
                'delivery_assigned' => $this->delivery_id ? true : false,
                'delivery_name' => $this->delivery?->name ?? null,
            ],

            // Products requested for refund
            'refund_products' => $refundItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->getFirstMediaUrl('product-images') ?? $item->product->image_url,
                                        'price' => (float) $item->price,
                    'discount' => (float) $item->discount_amount,
                    'price_after_discount' => (float) round(($item->price - $item->discount_amount), 2),
                    'quantity' => (int) $item->quantity,
                    'refund_quantity' => (int) ($item->refund_quantity ?? $item->quantity),
                    'total' => (float) $item->price  * ($item->refund_quantity ?? $item->quantity) ,
                ];
            })->values(),

            // Delivery/Pickup information
            'pickup_info' => [
                // Customer name (always from user)
                'customer_name' => $this->user?->name,
                
                // Use address data if available, otherwise fallback to gift data
                'address_name' => $this->address?->address_name ?? $this->gift_address_name,
                'recipient_name' => $this->address?->recipient_name ?? $this->reciver_name ?? $this->user?->name,
                'phone' => $this->address?->phone ?? $this->reciver_phone ?? $this->user?->phone,
                
                // City and district - from address or fallback to order's city relationship
                'city' => $this->address?->city?->name ?? $this->city?->name,
                'district' => $this->address?->district?->name ?? null,
                
                // Address description
                'address_description' => $this->address?->description ?? null,
                
                // Full address
                'full_address' => $this->address 
                    ? trim(($this->address->address_name ?? '') . ' ' . ($this->address->description ?? ''))
                    : $this->gift_address_name,
                
                // Coordinates - from address or fallback to order's gift coordinates
                'latitude' => $this->address?->latitude ?? $this->gift_latitude ?? $this->address_latitude,
                'longitude' => $this->address?->longitude ?? $this->gift_longitude ?? $this->address_longitude,
             
            ],
            // Additional info
            // 'notes' => $this->notes,
            'images' => MediaResource::collection($this->getMedia('refund_images')),
            
        ];
    }
}
