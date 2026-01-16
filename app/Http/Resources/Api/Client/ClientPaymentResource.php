<?php

namespace App\Http\Resources\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'invoice_number'    => $this->order_number, // or $this->invoice_number if stored
            'total'             => $this->total,        // or use final_total
            'payment_method'    => $this->paymentMethod->name ?? null,
            'payment_reference' => $this->payment_reference ?? null, // watch spelling
            'payment_due_date'  => $this->created_at?->toDateString(),
        ];
    }
}
