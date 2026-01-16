<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRefundResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'delivery_id'   => $this->delivery_id,
            'refund_number' => $this->refund_number,
            'amount'        => $this->amount,
            'status'        => $this->status,
            'reason'        => $this->reason,
            'created_at'    => $this->created_at ? $this->created_at->format('Y/m/d - H:i:s') : null,
            'images'        => $this->whenLoaded('images', function() {
                // If using Spatie Media Library
                if (method_exists($this, 'getMedia')) {
                    return $this->getMedia('images')->map(function($media) {
                        return $media->getFullUrl();
                    })->toArray();
                }
                // If images is a relation or attribute
                if (is_array($this->images)) {
                    return array_map(function($img) {
                        return is_string($img) ? $img : ($img['url'] ?? null);
                    }, $this->images);
                }
                return $this->images ?? [];
            }),
        ];
    }
}
