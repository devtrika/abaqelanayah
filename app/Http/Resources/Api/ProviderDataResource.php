<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Provider Data Resource for provider-specific information
 *
 * Response structure includes all provider fields and media resources
 */
class ProviderDataResource extends JsonResource
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
            'commercial_name' => $this->getTranslations('commercial_name'),
            'salon_type' => __('admin.' . $this->salon_type),
            'gender' => __('apis.' . $this->user->gender),
            'nationality' => __('admin.'.$this->nationality),
            'nationality_enum' => $this->nationality,



            'residence_type' => __('admin.' . $this->residence_type),
        
            'commercial_register_no' => $this->commercial_register_no,
            'sponsor_name' => $this->sponsor_name,
            'sponsor_phone' => $this->sponsor_phone,
            'institution_name' => $this->institution_name,
            'wallet_balance' => $this->wallet_balance,
            'withdrawable_balance' => $this->withdrawable_balance,
            'is_mobile' => (bool) $this->is_mobile,
            'accept_orders' => (bool) $this->accept_orders,
            'status' => $this->status,

            // Service capabilities
            'in_home' => (bool) $this->in_home,
            'in_salon' => (bool) $this->in_salon,
            'home_fees' => (float) $this->home_fees,

            // Availability information
            'lat' => $this->lat,
            'lng' => $this->lng,

            // Media resources
            'logo' =>  new MediaResource($this->getFirstMedia('logo')),
        
            'residence_image' =>  new MediaResource($this->getFirstMedia('residence_image')),
            'commercial_register_image' => new MediaResource($this->getFirstMedia('commercial_register_image')),
            'salon_images' => MediaResource::collection($this->getMedia('salon_images')),
        ];
    }
}
