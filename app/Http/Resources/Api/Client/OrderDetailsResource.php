<?php

namespace App\Http\Resources\Api\Client;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;
use App\Http\Resources\OrderRatingResource;
use App\Http\Resources\Api\ProblemResource;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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

    public function toArray($request)
    {
        $paymentMethod = \App\Models\PaymentMethod::find($this->payment_method_id);
        $vatPercent = (float) (\App\Models\SiteSetting::where('key', 'vat_amount')->value('value') ?? 0);

        // Compute products total after discount and VAT based on that
        $productsTotalAfterDiscount = (float) ($this->subtotal - $this->discount_amount);
        $vatAmount = (float) round(($productsTotalAfterDiscount * ($vatPercent)), 2);

        // Determine if showing refund items only
        $showRefundItemsOnly = $this->isDeliveryRefund && $this->refundable;

        // Resolve coupon data from relation; fallback to lookup by coupon_code if relation missing
        $couponModel = $this->coupon ?? null;
        if (!$couponModel && !empty($this->coupon_code)) {
            $couponModel = \App\Models\Coupon::where('coupon_num', $this->coupon_code)->first();
        }
        $couponType = $couponModel?->type;
        $couponValue = $couponModel?->discount !== null ? (float) $couponModel->discount : null;
    $currentRoute = $request->route()?->getName();

          if (str_starts_with($currentRoute, 'client.refundable_orders.')) {
        $effectiveStatus = $this->refund_status ?: $this->status;
    } elseif (str_starts_with($currentRoute, 'client.orders.')) {
        $effectiveStatus = $this->status;
    } else {
        $effectiveStatus = $this->status;
    }

        return [
            // Order Info
            'order_number' => $this->formatOrderNumber($this->order_number),
            'client_name'  => $this->user?->name,
            'created_at' => $this->created_at->format('Y/m/d - H:i'),
            // Prefer refund_status when present to reflect refund lifecycle in client view
        'status_enum' => \App\Enums\OrderStatus::tryFrom($effectiveStatus)?->value,
        'status' => __('admin.' . $effectiveStatus),

            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                // 'city' => optional($this->branch->city)->name,
                'latitude' => $this->branch->latitude,
                'longitude' => $this->branch->longitude,
                'expected_duration' => $this->branch->expected_duration,
                'last_order_time' => $this->branch->last_order_time,
            ] : null,
            'delivery_type' => $this->delivery_type,
            'delivery_type_text' => __('admin.' . $this->delivery_type),
            'order_type' => $this->order_type ? __('admin.' . $this->order_type) : null,

            // Payment information
            'payment_info' => [
                'payment_method' => $this->paymentMethod?->name,
                'status' => $this->payment_status,
                'payment_date' => $this->created_at->format('y-m-d h:i:s'),
                'invoice_number' => $this->invoice_number ?? $this->order_number,
                'payment_link' => $this->payment_url
            ],


            'address' => $this->whenLoaded('address', function () {
                return AddressResource::make($this->address);
            }),



            // Products
            'products' => $this->whenLoaded('items', function () use ($showRefundItemsOnly) {
                // Filter items if delivery refund context
                $items = $showRefundItemsOnly
                    ? $this->items->where('request_refund', true)
                    : $this->items;

                return $items->map(function ($item) {
                    return [
                        'id'  => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image_url ?? null,
                        'price' => (float) $item->price,
                        'discount' => (float) $item->discount_amount,
                        'price_after_discount' => (float) round(($item->price - $item->discount_amount), 2),
                        'quantity' => (int) $item->quantity,
                        'total' => (float) $item->total,
                        'is_refunded' => $item->request_refund == 1 ? false : (bool) $item->product->is_refunded
                                     ];
                });
            }),


            'cost_details' => [
                'items_count' => $this->items ? $this->items->sum('quantity') : 0,
                'products_total_without_vat' => (float) $this->subtotal,
                'subtotal' => (float) $this->subtotal,
                'coupon_code' => $this->coupon_code,
                'coupon_value' => $couponValue,
                // Use Order model accessor coupon_amount (falls back to discount_amount) so
                // order details always include the numeric coupon amount like the cart.
                'coupon_amount' => $this->coupon_amount,
                'coupon_type' => $couponType,
                'discount' => (float) $this->discount_amount,
                'products_total_after_discount' => $productsTotalAfterDiscount,
                // VAT is calculated on products total after discount
                // 'vat_amount' => $vatAmount,
                'vat_amount' => $this->vat_amount,
                'vat_percent' => $vatPercent,
                'total_with_vat' => (float) round($productsTotalAfterDiscount + $vatAmount, 2),
                'total_without_vat' => (float) ($productsTotalAfterDiscount),
                'delivery_fee' => (float) $this->delivery_fee,
                'gift_fee' => (float) ($this->gift_fee ?? 0),
                // 'products_total_without_vat' is intentionally kept only once (duplicate removed)
                'wallet_deduction' => $this->wallet_deduction,
                'total' => $this->total,
            ],

            // Gift / recipient info (only for gift orders)
            'gift_details' => $this->when($this->order_type === 'gift', [
                'reciver_name' => $this->reciver_name ?? null,
                'reciver_phone' => $this->reciver_phone ?? null,
                'gift_address_name' => $this->gift_address_name ?? null,
                'gift_latitude' => isset($this->gift_latitude) ? (float) $this->gift_latitude : null,
                'gift_longitude' => isset($this->gift_longitude) ? (float) $this->gift_longitude : null,
                'message' => $this->message ?? null,
                'whatsapp' => isset($this->whatsapp) ? (bool) $this->whatsapp : false,
                'hide_sender' => isset($this->hide_sender) ? (bool) $this->hide_sender : false,
            ]),

            // Scheduled order fields
            'schedule_date' => isset($this->schedule_date) && $this->schedule_date ? (
                $this->schedule_date instanceof \DateTimeInterface
                ? $this->schedule_date->format('Y-m-d')
                : (($ts = strtotime($this->schedule_date)) ? date('Y-m-d', $ts) : (string) $this->schedule_date)
            ) : null,
            'schedule_time' => isset($this->schedule_time) && $this->schedule_time ? (
                $this->schedule_time instanceof \DateTimeInterface
                ? $this->schedule_time->format('H:i')
                : (($ts = strtotime($this->schedule_time)) ? date('H:i', $ts) : (string) $this->schedule_time)
            ) : null,



            'problem' => (function () {
                if ($this->status !== 'problem') {
                    return null;
                }
                if (!empty($this->notes)) {
                    return $this->notes;
                }
                if ($this->problem_id) {
                    $problem = $this->whenLoaded('problem') ? $this->problem : (\App\Models\Problem::find($this->problem_id));
                    return $problem?->problem ?? null;
                }
                return null;
            })(),

            'cancelReason' => (function () {
                if ($this->status !== 'cancelled') {
                    return null;
                }
                if ($this->cancel_reason_id) {
                    $cr = $this->whenLoaded('cancelReason') ? $this->cancelReason : (\App\Models\CancelReason::find($this->cancel_reason_id));
                    return $cr?->reason ?? null;
                }
                if (!empty($this->notes)) {
                    return $this->notes;
                }
                return null;
            })(),


            'order_ratings' => OrderRatingResource::collection($this->rate),



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
