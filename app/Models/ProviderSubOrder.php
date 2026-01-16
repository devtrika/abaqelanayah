<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderSubOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider_id',
        'sub_order_number',
        'status',
        'subtotal',
        'services_total',
        'products_total',
        'booking_fee',
        'home_service_fee',
        'delivery_fee',
        'discount_amount',
        'total',
        'provider_share',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'services_total' => 'decimal:2',
        'products_total' => 'decimal:2',
        'booking_fee' => 'decimal:2',
        'home_service_fee' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'provider_share' => 'decimal:2',
    ];

    /**
     * Generate unique sub-order number
     */
    public static function generateSubOrderNumber()
    {
        do {
            $number = 'PSO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('sub_order_number', $number)->exists());

        return $number;
    }

    /**
     * Get the main order that owns this sub-order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the provider for this sub-order
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class)->withTrashed();
    }

    /**
     * Get the order items for this provider
     */
    public function orderItems()
    {
        $providerId = $this->provider_id;

        return $this->hasMany(OrderItem::class, 'order_id', 'order_id')
                    ->where(function($query) use ($providerId) {
                        $query->where(function($subQuery) use ($providerId) {
                            // For services
                            $subQuery->where('item_type', 'App\Models\Service')
                                    ->whereHas('service', function($serviceQuery) use ($providerId) {
                                        $serviceQuery->where('provider_id', $providerId);
                                    });
                        })->orWhere(function($subQuery) use ($providerId) {
                            // For products
                            $subQuery->where('item_type', 'App\Models\Product')
                                    ->whereHas('product', function($productQuery) use ($providerId) {
                                        $productQuery->where('provider_id', $providerId);
                                    });
                        });
                    });
    }

    /**
     * Get all status changes for this sub-order
     */
    public function statusChanges()
    {
        return $this->hasMany(OrderStatus::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest status change
     */
    public function latestStatusChange()
    {
        return $this->hasOne(OrderStatus::class)->latestOfMany();
    }

    /**
     * Update sub-order status and track the change
     */
    public function updateStatus($newStatus, $statusableType, $statusableId)
    {
        $updateData = ['status' => $newStatus];

        $this->update($updateData);

        // Track the status change
        OrderStatus::create([
            'provider_sub_order_id' => $this->id,
            'status' => $newStatus,
            'statusable_type' => $statusableType,
            'statusable_id' => $statusableId,
        ]);

        // Update main order status based on all sub-orders
        $this->updateMainOrderStatus();

        return $this;
    }

    /**
     * Update main order status based on all sub-orders
     */
    private function updateMainOrderStatus()
    {
        $order = $this->order;
        $allSubOrders = $order->providerSubOrders;

        // Get all sub-order statuses
        $statuses = $allSubOrders->pluck('status')->toArray();

        // Determine overall order status
        if (empty($statuses)) {
            $newStatus = 'pending_payment';
        } elseif (collect($statuses)->every(fn($status) => $status === 'cancelled')) {
            $newStatus = 'cancelled';
        } elseif (in_array('cancelled', $statuses)) {
            $newStatus = 'partially_cancelled';
        } elseif (collect($statuses)->every(fn($status) => $status === 'completed')) {
            $newStatus = 'completed';
        } elseif (collect($statuses)->every(fn($status) => in_array($status, ['completed', 'confirmed']))) {
            $newStatus = 'confirmed';
        } elseif (in_array('processing', $statuses) || in_array('confirmed', $statuses)) {
            $newStatus = 'processing';
        } else {
            $newStatus = 'pending_payment';
        }

        // Only update if status actually changed
        if ($order->status !== $newStatus) {
            $order->update(['current_status' => $newStatus]);
        }
    }

    /**
     * Check if this sub-order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending_payment', 'processing', 'confirmed']);
    }

    /**
     * Get status description
     */
    public function getStatusDescriptionAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending_payment' => 'warning',
            'processing' => 'info',
            'confirmed' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by provider
     */
    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }
}
