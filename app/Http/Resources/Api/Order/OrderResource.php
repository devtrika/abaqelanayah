<?php

namespace App\Http\Resources\Api\Order;

use App\Enums\OrderStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @var bool Whether this is for delivery refund context
     */
    protected $isDeliveryRefund = false;

    /**
     * Set if this is a delivery refund context
     */
    public function forDeliveryRefund($value = true)
    {
        $this->isDeliveryRefund = $value;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
public function toArray($request)
{
    // 👇 نجيب اسم الـ route الحالي
    $currentRoute = $request->route()?->getName();

    // تحديد العناصر
    $showRefundItemsOnly = $this->isDeliveryRefund && $this->refundable;

    $items = $showRefundItemsOnly
        ? $this->items->where('request_refund', true)
        : $this->items;

    if (str_starts_with($currentRoute, 'client.refundable_orders.')) {
        $effectiveStatus = $this->refund_status ?: $this->status;
    } elseif (str_starts_with($currentRoute, 'client.orders.')) {
        $effectiveStatus = $this->status;
    } else {
        $effectiveStatus = $this->status;
    }

    return [
        'id' => $this->id,
        'order_number' => $this->formatOrderNumber($this->order_number),
        'refund_number' => $this->when($this->refundable, $this->refund_number),
        'status' => __('admin.' . $effectiveStatus),
        'order_type' => __('admin.' . $this->order_type),
        'order_type_enum' => $this->order_type,
        'latitude' => $this->address?->latitude ?? $this->gift_latitude,
        'longitude' => $this->address?->longitude ?? $this->gift_longitude,
        'user' => [
            'id' => $this->user_id,
            'name' => $this->user?->name,
            'phone' => $this->user?->phone,
        ],
        'status_enum' => \App\Enums\OrderStatus::tryFrom($effectiveStatus)?->value,
        'date' => $this->created_at->format('Y-m-d h:i:s'),
        'image' => $items->first()?->product?->getFirstMediaUrl('product-images') ?? null,
        'total' => $this->refundable ? (float) $this->refund_amount : (float) $this->total,
        // 'refundable' => (bool) $this->refundable,
    ];
}




    /**
     * Format order number for shorter display.
     * Examples:
     *  - ORD-1761737250-2302 => ORD-2302
     *  - other formats remain unchanged
     */
    private function formatOrderNumber(?string $orderNumber): ?string
    {
        if (empty($orderNumber)) {
            return $orderNumber;
        }

        $parts = explode('-', $orderNumber);
        if (count($parts) >= 3 && strtoupper($parts[0]) === 'ORD') {
            $last = end($parts);
            return 'ORD-' . $last;
        }

        return $orderNumber;
    }

}
