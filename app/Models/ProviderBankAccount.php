<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'bank_name',
        'holder_name',
        'bank_name',
        'account_number',
        'iban',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the provider that owns the bank account
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}


