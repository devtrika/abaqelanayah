<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAffiliateData extends Model
{
    protected $fillable = [
        'user_id', 'beneficiary_name', 'bank_name', 'account_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
