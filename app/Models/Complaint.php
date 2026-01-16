<?php

namespace App\Models;


class Complaint extends BaseModel
{
    protected $fillable = ['user_name' , 'user_id' , 'complaint' , 'phone' , 'email']; 
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function replays()
    {
        return $this->hasMany(ComplaintReplay::class, 'complaint_id', 'id');
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
                } elseif (str_contains($key, '_id')) {
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
