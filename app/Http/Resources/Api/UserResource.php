<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
  private $token               = '';

  public function setToken($value) {
    $this->token = $value;
    return $this;
  }

  public function toArray($request) {
    return [
      'id'                  => $this->id,
      'name'                => $this->name,
      'email'               => $this->email,
      'country_code'        => $this->country_code,
      'type'=> $this->type,
      'phone'               => $this->phone,
      'full_phone'          => $this->full_phone,
      'image'               => $this->image,
      'lang'                => $this->lang,
      'is_notify'           => $this->is_notify,
      'accept_orders' => (bool) $this->accept_orders,
      'city' => [
        'id'=>$this?->city_id,
        'name' => $this->city?->name
        ],
        'district' => [
        'id'=>$this?->district_id,
        'name' => $this->district?->name
        ],
        
        
        //   'region' => [
        // 'id'=>$this?->region_id,
        // 'name' => $this->region?->name
        // ],
      'token'               => $this->token,
    ];
  }
}
