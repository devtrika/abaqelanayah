<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // 'points'     => $this->getPoints(),
            'amount'     => number_format($this->amount, 2) . ' ر.س',
            'note'       => $this->parseNote(),
        ];
    }

    // private function getPoints()
    // {
    //     return in_array($this->type, ['loyality_reward', 'loyality_spent'])
    //         ? (int) $this->amount
    //         : null;
    // }

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
            return $note[$locale] ?? $note['en'] ?? $note['ar'] ?? '-';
        }

        return $note ?: '-';
    }
}
