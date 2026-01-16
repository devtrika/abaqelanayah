<?php
namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        $vatPercent = (float) (\App\Models\SiteSetting::where('key', 'vat_amount')->value('value') ?? 0);
        $giftFee = (float) (\App\Models\SiteSetting::where('key', 'gift_fee')->value('value') ?? 0);

        // Calculate totals used below to avoid undefined variable errors
        $productsTotalAfterDiscount = (float) ((float) ($this->subtotal ?? 0) - (float) ($this->discount ?? 0));
        $vatAmount = (float) ($this->vat_amount ?? 0);

        return [
            'id' => $this->id,

            // Products - matching OrderDetailsResource format
            'products' => $this->items->map(function($item) {
                return [
                    'id'  => $item->product->id,
                    'name' => $item->product->name,
                    'image' => $item->product->image_url ?? null,
                    'price' => (float) $item->price,
                    'discount' => (float) $item->discount_amount,
                    'price_after_discount' => (float) round(($item->price - $item->discount_amount), 2),
                    'quantity' => (int) $item->quantity,
                    'total' => (float) $item->total,
                    'is_refunded' => (bool) ($item->product->is_refunded ?? false),
                ];
            }),

            // Cost Details - matching OrderDetailsResource format exactly
            'cost_details' => [
                'items_count' => $this->items ? $this->items->sum('quantity') : 0,
                'products_total_without_vat' => (float) $this->subtotal,
                'subtotal' => (float) $this->subtotal,
                'coupon_code' => $this->coupon_code,
                'coupon_value' => $this->coupon?->type === 'ratio'
                    ? (int) $this->coupon?->discount . '%'
                    : (int) $this->coupon?->discount,
                'coupon_amount' => $this->coupon_value,
                'gift_fee' => $giftFee ,

                'coupon_type' => $this->coupon?->type ?? null,
                'discount' => (float) $this->discount,
                'products_total_after_discount' => $productsTotalAfterDiscount,
                'vat_amount' => (float) $this->vat_amount,
                'vat_percent' => $vatPercent,
                'total_with_vat' => (float) round($productsTotalAfterDiscount + $vatAmount, 2),
                'total_without_vat' => (float) ($productsTotalAfterDiscount),
                'delivery_fee' => (float) ($this->delivery_fee ?? 0),
                'wallet_deduction' => (float) ($this->wallet_deduction ?? 0),
                'total' => (float) $this->total,
            ],
        ];
    }
}