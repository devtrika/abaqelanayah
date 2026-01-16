<?php

namespace App\Models;

use App\Traits\HasAutoMedia;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends BaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, HasAutoMedia;

    // Payment type constants
    const PAY_TYPE_ONLINE = 'online';
    const PAY_TYPE_WALLET = 'wallet';
    const PAY_TYPE_CASH = 'cash';

    // Payment status constants
    const PAY_STATUS_PENDING = 'pending';
    const PAY_STATUS_DONE = 'done';
    const PAY_STATUS_PAID = 'paid';
    const PAY_STATUS_RETURNED = 'returned';

    protected $fillable = [
        'order_number',
        'user_id',

        'address_id',
        'city_id',
        'delivery_id',
        'address_latitude',
        'address_longitude',
        'status',
        'payment_method_id',
        'payment_status',
        'subtotal',
        'schedule_date',
        'schedule_time',
        'services_total',
        'products_total',
        'discount_amount',
        'discount_code',
        'coupon_amount',
        'vat_amount',
        'payment_status',
        'discount_percentage',
    'coupon_id',
    'discount_code',
        'booking_fee',
        'home_service_fee',
        'delivery_fee',
        'gift_fee',
        'platform_commission',
        'order_type',
        'total',
        'cancel_fees',
        'amount_paid',
        'recipient_name',
        // 'loyalty_points_earned',
        // 'loyalty_points_used',
        'wallet_deduction',
        // 'loyalty_deduction',
        'cancellation_reason',
        'scheduled_at',
        'acceptance_deadline',
        'invoice_number',
        'payment_reference',
        'payment_url',
        'payment_date',
        'booking_type',
        'delivery_type',
        'bank_account_id',
        'cancel_reason_id',
        'time'  ,
        'branch_id',
        'refund_status',
        'problem_id',
        'notes',
        // Gift order fields
    'reciver_name',
    'reciver_phone',
        'gift_address_name',
        'gift_city_id',
        'gift_districts_id',
        'gift_latitude',
        'gift_longitude',
        'message',
        'whatsapp',
        'hide_sender',
        // Refund fields
        'is_refund',
        'refundable',
        'original_order_id',
        'refund_number',
        'refund_reason_id',
        'refund_reason_text',
        'refund_amount',
        'refund_requested_at',
        'refund_approved_at',
        'refund_rejected_at',
        'refund_approved_by',
        'refund_rejected_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'services_total' => 'decimal:2',
        'products_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'coupon_amount' => 'decimal:2',
        'wallet_deduction' => 'decimal:2',
        // 'loyalty_deduction' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'booking_fee' => 'decimal:2',
        'home_service_fee' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'gift_fee' => 'decimal:2',
        'platform_commission' => 'decimal:2',

        'total' => 'decimal:2',

        'amount_paid' => 'decimal:2',

        'address_latitude' => 'decimal:8',
        'address_longitude' => 'decimal:8',
    'gift_latitude' => 'decimal:8',
    'gift_longitude' => 'decimal:8',
        'loyalty_points_earned' => 'integer',
        'loyalty_points_used' => 'integer',
        'scheduled_at' => 'datetime',
        'acceptance_deadline' => 'datetime',
        'payment_date' => 'datetime',
        // Refund casts
        'is_refund' => 'boolean',
        'refundable' => 'boolean',
        'refund_amount' => 'decimal:2',
        'refund_requested_at' => 'datetime',
        'refund_approved_at' => 'datetime',
        'refund_rejected_at' => 'datetime',
    ];


        protected array $autoMedia = [
        // Single file: request field "image" -> collection "product_image"
        'image'   => 'refund_image',
        // Multiple files: request field "gallery[]" -> collection "product_gallery"
        'images' => ['collection' => 'refund_images', 'multiple' => true],

    ];
    /**
     * Get the user that owns the order
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    

    public function cancelReason()
    {
        return $this->belongsTo(CancelReason::class);
    }

    /**
     * Get the address for the order
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the city for the order (for gift orders without address)
     */
    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    /**
     * Gift city relationship for gift orders without address
     */
    public function giftCity()
    {
        return $this->belongsTo(\App\Models\City::class, 'gift_city_id');
    }

    /**
     * Gift district relationship for gift orders without address
     */
    public function giftDistrict()
    {
        return $this->belongsTo(\App\Models\District::class, 'gift_districts_id');
    }

       public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
    /**
     * Get the coupon applied to the order
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Accessor to provide backwards-compatible coupon_code property
     * which maps to the stored discount_code column.
     */
    public function getCouponCodeAttribute()
    {
        return $this->attributes['discount_code'] ?? null;
    }

    /**
     * Accessor to provide coupon_amount which maps to discount_amount
     */
    public function getCouponAmountAttribute()
    {
        // Prefer an explicit coupon_amount column when present (newer behavior).
        // Fall back to discount_amount for backward compatibility.
        if (array_key_exists('coupon_amount', $this->attributes)) {
            return $this->attributes['coupon_amount'] ?? 0;
        }

        return $this->attributes['discount_amount'] ?? 0;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [ 'processing']);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
        public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }


    // Assigned delivery user (delivery_id)
    public function deliveryUser()
    {
        // The assigned delivery user is stored in the delivery_id column on orders
        // Include trashed users for history display consistency
        return $this->belongsTo(User::class, 'delivery_id')->withTrashed()->where('type', 'delivery');
    }

    public function rate()
    {
        return $this->hasMany(OrderRating::class);
    }
// public function rates()
// {
//     return $this->hasMany(Rate::class, 'order_id');
// }

public function rates()
{
    return $this->hasMany(OrderRating::class);
}







    
    public function statusChanges()
    {
        return $this->hasMany(OrderStatus::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the refund request for this order (OLD - kept for backward compatibility)
     * @deprecated Use refundOrders() instead
     */
    public function refundOrder()
    {
        return $this->hasOne(RefundOrder::class);
    }

    /**
     * Get the original order if this is a refund order
     */
    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'original_order_id');
    }

    /**
     * Get all refund orders created from this order
     */
    public function refundOrders()
    {
        return $this->hasMany(Order::class, 'original_order_id');
    }

    /**
     * Get the refund reason
     */
    public function refundReason()
    {
        return $this->belongsTo(\App\Models\RefundReason::class, 'refund_reason_id');
    }

    /**
     * Get the admin who approved the refund
     */
    public function refundApprovedBy()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'refund_approved_by');
    }

    /**
     * Get the admin who rejected the refund
     */
    public function refundRejectedBy()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'refund_rejected_by');
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
     * Check if this order is a refund order
     */
    public function isRefund(): bool
    {
        return (bool) $this->is_refund;
    }

    /**
     * Check if this order has any refund requests
     */
    public function hasRefundRequests(): bool
    {
        return $this->refundOrders()->exists();
    }


}
