<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRatingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'order_id'   => $this->order_id,
        ];
    }
}
