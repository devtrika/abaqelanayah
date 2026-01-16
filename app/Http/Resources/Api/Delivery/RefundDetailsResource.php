<?php

namespace App\Http\Resources\Api\Delivery;

use Illuminate\Http\Resources\Json\JsonResource;

class RefundDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'refund_info' => [
                'id'            => $this->id,
                'order_id'      => $this->order_id,
                'delivery_id'   => $this->delivery_id,
                'refund_number' => $this->refund_number,
                'amount'        => (float) $this->amount,
                'status'        => [
                    'key'   => $this->status,
                    'label' => $this->getStatusLabelAttribute(),
                    'color' => $this->getStatusBadgeColorAttribute(),
                ],
                'reason'        => $this->reason,
                'created_at'    => $this->created_at->format('Y/m/d - H:i'),
            ],

            'products_requested' => $this->order && $this->order->items
                ? $this->order->items->map(function ($item) {
                    return [
                        'product_id'   => $item->product_id,
                        'name'         => optional($item->product)->name,
                        'image'        => $item->product->image_url ?? null,
                        'price'        => (float) $item->price,
                        'quantity'     => (int) $item->quantity,
                        'total'        => (float) $item->total,
                        'is_refunded'  => (bool) ($item->product->is_refunded ?? false),
                        'discount'     => (float) $item->discount_amount,
                        'status'       => $item->status ?? null,
                    ];
                })
                : [],

            'products_accepted' => $this->order && $this->order->items
                ? $this->order->items
                    ->filter(fn($item) => ($item->status ?? null) === 'accepted')
                    ->map(function ($item) {
                        return [
                            'product_id'   => $item->product_id,
                            'name'         => optional($item->product)->name,
                            'image'        => $item->product->image_url ?? null,
                            'price'        => (float) $item->price,
                            'quantity'     => (int) $item->quantity,
                            'total'        => (float) $item->total,
                            'is_refunded'  => (bool) ($item->product->is_refunded ?? false),
                            'discount'     => (float) $item->discount_amount,
                            'status'       => $item->status,
                        ];
                    })
                : [],
        ];
    }
}
