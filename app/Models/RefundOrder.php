<?php

namespace App\Models;

use App\Traits\HasAutoMedia;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefundOrder extends BaseModel implements HasMedia
{
    use HasFactory , HasAutoMedia;

    protected $fillable = [
        'order_id',
        'delivery_id',
        'refund_number',
        'amount',
        'status',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'new';
    public const STATUS_REFUSED = 'refused';

    /**
     * Get the order that this refund belongs to
     */


      protected array $autoMedia = [
        // Single file: request field "image" -> collection "product_image"
        'image'   => 'refund_image',
        // Multiple files: request field "gallery[]" -> collection "product_gallery"
        'images' => ['collection' => 'refund_images', 'multiple' => true],

    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the delivery person assigned to this refund
     */
    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }

    /**
     * Get the user who requested the refund (through order relationship)
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Order::class, 'id', 'id', 'order_id', 'user_id');
    }

    /**
     * Generate a unique refund number
     */
    public static function generateRefundNumber()
    {
        do {
            $refundNumber = 'REF-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('refund_number', $refundNumber)->exists());

        return $refundNumber;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get status badge color for admin interface
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REFUSED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label for admin interface
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => __('admin.pending'),
            self::STATUS_ACCEPTED => __('admin.accepted'),
            self::STATUS_REFUSED => __('admin.refused'),
            default => $this->status,
        };
    }

    /**
     * Search scope for refund orders
     */
    public function scopeSearch($query, $searchArray = [])
    {
        $searchArray = is_array($searchArray) ? $searchArray : [];

        $query->where(function ($q) use ($searchArray) {
            foreach ($searchArray as $key => $value) {
                if (is_null($value) || $value === '') continue;

                switch ($key) {
                    case 'refund_number':
                        $q->where('refund_number', 'like', "%{$value}%");
                        break;
                    case 'order_number':
                        $q->whereHas('order', function ($orderQuery) use ($value) {
                            $orderQuery->where('order_number', 'like', "%{$value}%")
                                      ->orWhere('order_num', 'like', "%{$value}%");
                        });
                        break;
                    case 'user_name':
                        $q->whereHas('order.user', function ($userQuery) use ($value) {
                            $userQuery->where('name', 'like', "%{$value}%");
                        });
                        break;
                    case 'status':
                        $q->where('status', $value);
                        break;
                    case 'created_at_min':
                        $q->whereDate('created_at', '>=', $value);
                        break;
                    case 'created_at_max':
                        $q->whereDate('created_at', '<=', $value);
                        break;
                    default:
                        if (str_contains($key, '_id')) {
                            $q->where($key, $value);
                        }
                        break;
                }
            }
        });

        $orderDirection = isset($searchArray['order']) ? $searchArray['order'] : 'DESC';
        return $query->orderBy('created_at', $orderDirection);
    }
}
