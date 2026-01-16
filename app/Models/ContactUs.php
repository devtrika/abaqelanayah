<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'name',
        'phone',
        'email',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $searchArray = [])
    {
        $searchArray = is_array($searchArray) ? $searchArray : [];

        $query->where(function ($q) use ($searchArray) {
            foreach ($searchArray as $key => $value) {
                if (is_null($value) || $value === '') continue;

                if ($key === 'user_name') {
                    // Search in the related user's name
                    $q->whereHas('user', function ($userQuery) use ($value) {
                        $userQuery->where('name', 'like', "%{$value}%");
                    });
                }elseif ($key === 'user_type') {
                    $q->whereHas('user', function($uq) use ($value) {
                        $uq->where('type', $value);
                    });
                }elseif ($key === 'type') {
                    $q->where('type', $value);
                }elseif (str_contains($key, '_id')) {
                    $q->where($key, $value);
                } elseif ($key === 'created_at_min') {
                    $q->whereDate('created_at', '>=', $value);
                } elseif ($key === 'created_at_max') {
                    $q->whereDate('created_at', '<=', $value);
                } elseif ($key !== 'order') {
                    $q->where($key, 'like', "%{$value}%");
                }
            }
        });

        $orderDirection = isset($searchArray['order']) ? $searchArray['order'] : 'DESC';

        return $query->orderBy('created_at', $orderDirection);
    }
}
