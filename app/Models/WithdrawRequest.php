<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WithdrawRequest extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'provider_id',
        'number',
        'amount',
        'status',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('withdraw_requests');
    }
}
