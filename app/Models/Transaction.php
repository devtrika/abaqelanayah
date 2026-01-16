<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class Transaction extends BaseModel
{
    use HasTranslations ;

    
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'order_id',
        'reference',
        'note',
        'bank_name',
        'account_holder_name',
        'account_number',
        'iban',
        'transfer_reference',
        'status',
        
    ];


    protected $casts = [
        'amount' => 'decimal:2',
            'note' => 'array',

    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order() : BelongsTo{
        return $this->belongsTo(Order::class);
    }
  
       public function parseNote()
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
