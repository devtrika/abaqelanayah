<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderNewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'order_id'   => $this->id,
            'order_num'  => $this->order_num,
            'order_date' => $this->created_at->format('Y-m-d H:i:s'),
            'user_name'  => $this->user?->name,
        ];
    }
}
