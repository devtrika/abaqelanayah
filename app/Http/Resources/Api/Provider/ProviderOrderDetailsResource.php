<?php

namespace App\Http\Resources\Api\Provider;

use App\Http\Resources\Api\OrderRateResource;
use App\Enums\OrderStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderOrderDetailsResource extends JsonResource
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
            'sub_order_number' => $this->sub_order_number,
            'order_number' => $this->order->order_number,
            'status' => __('admin.' . $this->status),
            'status_enum' => OrderStatus::from($this->status),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('d M Y, h:i A'),

            // Customer Information
            'customer' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
                'phone' => $this->order->user->phone,
                'country_code' => $this->order->user->country_code,
                'full_phone' => $this->order->user->country_code . $this->order->user->phone,
                'image' => $this->order->user->image ?? null,
            ],

            // Address Information
            'address' => $this->order->address ? [
                'id' => $this->order->address->id,
                'details' => $this->order->address->details,
            ] : null,

            // Order Items (Services and Products for this provider)
            'items' => $this->getProviderItems(),

            // Financial Details
            'financial_details' => [
                'subtotal' => (float) $this->subtotal,
                'services_total' => (float) $this->services_total,
                'products_total' => (float) $this->products_total,
                'booking_fee' => (float) $this->booking_fee,
                'home_service_fee' => (float) $this->home_service_fee,
                'delivery_fee' => (float) $this->delivery_fee,
                'discount_amount' => (float) $this->discount_amount,
                'total' => (float) $this->total,
            ],

            // Payment Information
            'payment_details' => [
                'payment_method' => $this->order->paymentMethod->name ?? null,
                'payment_status' => $this->order->payment_status,
                'payment_reference' => $this->order->payment_reference,
                'due_date' => $this->order->due_date ? $this->order->due_date->format('Y-m-d H:i:s') : null,
            ],

            // Booking Information
            'booking_details' => [
                'booking_type' => $this->order->booking_type,
                'delivery_type' => $this->order->delivery_type,
                'scheduled_at' => $this->order->scheduled_at ? $this->order->scheduled_at->format('Y-m-d')  . ' ' . $this->order->time : null,
            ],
            'rate' => $this->when($this->order->current_status == 'completed' , OrderRateResource::make($this->order->rate)),

            // Order History/Status Changes
            'status_history' => $this->statusChanges->map(function ($status) {
                return [
                    'status' => __('admin.' . $status->status),
                    'created_at' => $status->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $status->created_at->format('d M Y, h:i A'),
                    'description' => $status->map_desc ?? ucfirst(str_replace('_', ' ', $status->status)),
                ];
            }),
        ];
    }

    /**
     * Get order items for this provider
     */
    private function getProviderItems()
    {
        // Get all order items from the main order
        $allItems = $this->order->items ?? collect();

        // Filter items that belong to this provider
        $providerItems = $allItems->filter(function ($item) {
            if (!$item->item) {
                return false;
            }

            // Check if the item belongs to this provider
            if (isset($item->item->provider_id)) {
                return $item->item->provider_id == $this->provider_id;
            }

            return false;
        });

        // Transform the items
        return $providerItems->map(function ($item) {
            $image = null;

            // Get image based on item type
            if ($item->item_type === 'App\Models\Product' && $item->item) {
                $image = $item->item->getFirstMediaUrl('product-images') ?: null;
            } elseif ($item->item_type === 'App\Models\Service' && $item->item) {
                $image =  null;
            }

            return [
                'id' => $item->id,
                'type' => $item->item_type === 'App\Models\Service' ? 'service' : 'product',
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'total' => (float) $item->total,
                'image' => $image,
            ];
        })->values();
    }
}
