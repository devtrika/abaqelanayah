<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource
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
            'orders_count' => $this->orders_count,
            'month' => $this->month->format('Y-m'),
            'month_name' => $this->month->format('F Y'),
            'progress' => round($this->progress ?? 0, 2),
            'user_order_count' => $this->user_order_count ?? 0,
            'is_achieved' => $this->is_achieved ?? false,
            'coupon' => $this->when(
                $this->is_achieved ?? false,
                function () {
                    return [
                        'id' => $this->coupon->id,
                        'coupon_num' => $this->coupon->coupon_num,
                        'type' => $this->coupon->type,
                        'discount' => $this->coupon->discount,
                        'max_discount' => $this->coupon->max_discount,
                        'discount_text' => $this->coupon->type === 'ratio'
                            ? $this->coupon->discount . '%'
                            : $this->coupon->discount . ' ' . __('apis.currency'),
                        'status' => $this->coupon->status,
                        'start_date' => $this->coupon->start_date,
                        'expire_date' => $this->coupon->expire_date,
                    ];
                }
            ),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
