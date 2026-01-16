<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Provider Resource with separated data structures
 *
 * Response structure:
 * {
 *   "id": 1,
 *   "name": "Provider Name",
 *   "email": "provider@example.com",
 *   "phone": "1234567890",
 *   "country_code": "966",
 *   "status": "active",
 *   "type": "provider",
 *   "image": { MediaResource },
 *   "id_image": { MediaResource },
 *   "license_image": { MediaResource },
 *   "city": { "id": 1, "name": "City Name" },
 *   "provider": { ProviderDataResource },
 *   "bank_account": { BankAccountResource },
 *   "token": "bearer_token"
 * }
 */
class ProviderResource extends JsonResource
{
    private $token = null;

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function toArray($request)
    {
        $data = [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'country_code' => $this->country_code,
            'status'       => $this->status,
            'type'         => $this->type,
            'is_notify' => $this->is_notify,

            // User media


            'city' => $this->city ? [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ] : null,

            'region' => $this->region ? [
                'id'   => $this->region->id,
                'name' => $this->region->name,
            ] : null,

            // Provider specific data using ProviderDataResource
            'provider' => $this->whenLoaded('provider', function() {
                return new ProviderDataResource($this->provider);
            }),

            // Bank account information using BankAccountResource
            'bank_account' => new BankAccountResource($this->provider->bankAccount)
            
        ];

        if ($this->token) {
            $data['token'] = $this->token;
        }

        return $data;
    }
}

