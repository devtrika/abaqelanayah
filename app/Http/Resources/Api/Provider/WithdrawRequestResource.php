<?php

namespace App\Http\Resources\Api\Provider;

use Illuminate\Http\Request;
use App\Http\Resources\Api\BankAccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\MediaResource;

class WithdrawRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'amount' => $this->amount,
            'status' => $this->status,
            'media' => MediaResource::make($this->getFirstMedia('withdraw_requests')),
            'bank_data' => BankAccountResource::make($this->provider->bankAccount),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
