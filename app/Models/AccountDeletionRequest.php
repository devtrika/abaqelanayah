<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountDeletionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reason',
        'status',
        'admin_notes',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];

    /**
     * Get the user that requested account deletion
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the admin who processed the request
     */
    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $searchArray)
    {
        if (!$searchArray) {
            return $query;
        }

        foreach ($searchArray as $key => $value) {
            if ($value !== null && $value !== '') {
                switch ($key) {
                    case 'user.name':
                        $query->whereHas('user', function ($q) use ($value) {
                            $q->where('name', 'like', '%' . $value . '%');
                        });
                        break;

                    case 'user.email':
                        $query->whereHas('user', function ($q) use ($value) {
                            $q->where('email', 'like', '%' . $value . '%');
                        });
                        break;

                    case 'user.phone':
                        $query->whereHas('user', function ($q) use ($value) {
                            $q->where('phone', 'like', '%' . $value . '%');
                        });
                        break;

                    case 'status':
                        if ($value !== '') {
                            $query->where('status', $value);
                        }
                        break;

                    case 'reason':
                        $query->where('reason', 'like', '%' . $value . '%');
                        break;

                    default:
                        // Handle direct column searches, but exclude system fields
                        if (strpos($key, '.') === false && !in_array($key, ['order', 'sort', 'direction', 'page', 'per_page'])) {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                        break;
                }
            }
        }

        return $query;
    }
}
