<?php

namespace App\Http\Resources\Api\Clinet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Client\ProductResource;
use App\Http\Resources\Api\Client\ServiceResource;

class ReferralLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'referral_code'   => $this->referral_code,
            'url'             => $this->url,
            'item' =>$this->getItemResource( $this->referrable),
            'created_at'      => $this->created_at->toDateTimeString(),
        ];
    }

    private function getItemResource($item)
    {
       

        if ($this->referrable_type === 'App\Models\Product') {
            return new ProductResource($item);
        } elseif ($this->referrable_type === 'App\Models\Service') {
            return new ServiceResource($item);
        }

        return null;
    }
}
