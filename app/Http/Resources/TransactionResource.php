<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $note = $this->parseNote();

        return [
            'id'         => $this->id,
            'amount'     => number_format($this->amount, 2),
            'note'       => $note,
            'order_number' => $this->when($this->order, $this->order?->order_number),
            'reference' => $this->reference,
            'bank_account' => $this->when($this->type == 'wallet-withdraw', function () {
            return [
                'bank_name' => $this->bank_name,
                'account_holder_name' => $this->account_holder_name,
                'account_number' => $this->account_number,
                'transfer_reference' => $this->transfer_reference
            ];
    }),
            'created_at' => $this->created_at->format('y-m-d h:i:s'),
        ];
    }

    private function parseNote()
    {
        $note = $this->note;

        if (is_string($note)) {
            $decoded = json_decode($note, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $note = $decoded;
            }
        }

        if (is_array($note)) {
            $locale = app()->getLocale() ?: 'en';
            return $note[$locale] ?? $note['en'] ?? $note['ar'] ?? '';
        }

        return $note ?? '';
    }
}
