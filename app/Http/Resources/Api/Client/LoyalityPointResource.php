<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyalityPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'loyalty_points_earned' => $this->when(request()->has('earn'),$this->loyalty_points_earned),
            'loyalty_points_used' => $this->when(!request()->has('earn'),$this->loyalty_points_used),

        ];
    }
}
