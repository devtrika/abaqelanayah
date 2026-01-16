<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bank Account Resource for provider bank account information
 *
 * Response structure:
 * {
 *   "id": 1,
 *   "holder_name": "Account Holder Name",
 *   "bank_name": "Bank Name",
 *   "account_number": "1234567890",
 *   "iban": "SA1234567890123456789012",
 *   "is_default": true
 * }
 */
class BankAccountResource extends JsonResource
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
            'holder_name' => $this->holder_name,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'iban' => $this->iban,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
