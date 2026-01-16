<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProviderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider_id',
        'status',
        'subtotal',
        'services_total',
        'products_total',
        'booking_fee',
        'home_service_fee',
        'delivery_fee',
        'discount_amount',
        'total',
        'notes',
        'confirmed_at',
        'completed_at',
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
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns this provider status
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the provider for this status
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the order items for this provider
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id')
                    ->whereHas('item', function($query) {
                        $query->where('provider_id', $this->provider_id);
                    });
    }

    /**
     * Update provider status and track the change
     */
    public function updateStatus($newStatus, $statusableType, $statusableId, $notes = null)
    {
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'confirmed') {
            $updateData['confirmed_at'] = now();
        } elseif ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
        }

        if ($notes) {
            $updateData['notes'] = $notes;
        }

        $this->update($updateData);

        // Track the status change in the main order status table
        OrderStatus::createStatusChange(
            $this->order_id,
            $newStatus,
            $statusableType,
            $statusableId,
            "Provider {$this->provider->name}: {$notes}"
        );

        // Update main order status based on all providers' statuses
        $this->updateMainOrderStatus();

        return $this;
    }

    /**
     * Update main order status based on all providers' statuses
     */
    private function updateMainOrderStatus()
    {
        $order = $this->order;
        $allProviderStatuses = $order->providerStatuses;

        // Determine overall order status based on provider statuses
        $statuses = $allProviderStatuses->pluck('status')->toArray();

        if (in_array('cancelled', $statuses)) {
            // If any provider cancelled, check if all are cancelled
            if (collect($statuses)->every(fn($status) => $status === 'cancelled')) {
                $newStatus = 'cancelled';
            } else {
                $newStatus = 'partially_cancelled';
            }
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
            $oldStatus = $order->status;
            $order->update(['status' => $newStatus]);
            
            // Send notification to user about status change
            $this->sendOrderStatusNotificationToUser($order, $newStatus);
        }
    }

    /**
     * Check if this provider's items can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending_payment', 'processing']);
    }

    /**
     * Get status description with provider context
     */
    public function getStatusDescriptionAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Send order status notification to user in both Arabic and English
     */
    private function sendOrderStatusNotificationToUser($order, $newStatus)
    {
        $user = $order->user;
        if ($user) {
            // Define status translations
            $statusTranslations = [
                'pending' => [
                    'ar' => 'قيد الانتظار',
                    'en' => 'Pending'
                ],
                'processing' => [
                    'ar' => 'جاري التجهيز',
                    'en' => 'Processing'
                ],
                'ready' => [
                    'ar' => 'تم التجهيز',
                    'en' => 'Ready'
                ],
                'delivering' => [
                    'ar' => 'جاري التوصيل',
                    'en' => 'Delivering'
                ],
                'waiting_pickup' => [
                    'ar' => 'في انتظار الاستلام',
                    'en' => 'Waiting for Pickup'
                ],
                'completed' => [
                    'ar' => 'مكتمل',
                    'en' => 'Completed'
                ],
                'cancelled' => [
                    'ar' => 'ملغي',
                    'en' => 'Cancelled'
                ],
                'confirmed' => [
                    'ar' => 'مؤكد',
                    'en' => 'Confirmed'
                ],
                'pending_payment' => [
                    'ar' => 'في انتظار الدفع',
                    'en' => 'Pending Payment'
                ],
                'partially_cancelled' => [
                    'ar' => 'ملغي جزئياً',
                    'en' => 'Partially Cancelled'
                ]
            ];

            $statusText = $statusTranslations[$newStatus] ?? [
                'ar' => $newStatus,
                'en' => $newStatus
            ];

            $user->notify(new \App\Notifications\NotifyUser([
                'title' => [
                    'ar' => 'تحديث حالة الطلب',
                    'en' => 'Order Status Update'
                ],
                'body' => [
                    'ar' => "تم تحديث حالة طلبك رقم #{$order->order_number} إلى {$statusText['ar']}",
                    'en' => "Your order #{$order->order_number} status has been updated to {$statusText['en']}"
                ],
                'type' => 'order_status_update',
                'order_id' => $order->id
            ]));
        }
    }
}
