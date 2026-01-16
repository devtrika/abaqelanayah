<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
  

    public function toArray($request)
    {
  

        return [
            'id' => $this->id,
            'address_name' =>$this->address_name,
            'recipient_name' =>$this->recipient_name,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
            'district' => [
                'id' => $this->districts_id,
                'name' => optional($this->district)->getTranslation('name', app()->getLocale()),
            ],
            'city' => [
                'id' => $this->city_id,
                'name' => optional($this->city)->name,
            ],
            'description'=>$this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
        ];
    }
}
