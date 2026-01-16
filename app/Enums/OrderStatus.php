<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';           // قيد الانتظار
    case PROCESSING = 'processing';     // جاري التجهيز
    case NEW = 'new';                   // جديد (تم تعيين مندوب التوصيل)
    case CONFIRMED = 'confirmed';       // تم التأكيد
    case DELIVERED = 'delivered';       // تم التوصيل
    case PROBLEM = 'problem';           // به مشكلة
    case CANCELLED = 'cancelled';       // ملغي
    case REQUEST_CANCEL = 'request_cancel';   // طلب الإلغاء
    case REQUEST_REFUND = 'request_refund';   // طلب استرجاع
    case REQUEST_REJECTED = 'request_rejected'; // تم رفض طلب الاسترجاع
    case REFUNDED = 'refunded';               // تم الاسترجاع

    case OUT_OF_DELIVERY = 'out-for-delivery';
    /**
     * Get all status values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status label
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::PROCESSING => 'جاري التجهيز',
            self::NEW => 'جديد',
            self::CONFIRMED => 'جاري التوصيل ',
            self::OUT_OF_DELIVERY => 'خارج للتسليم',
            self::DELIVERED => 'تم التوصيل',
            self::PROBLEM => 'به مشكلة',
            self::CANCELLED => 'ملغي',
            self::REQUEST_CANCEL => 'طلب الإلغاء',
            self::REQUEST_REFUND => 'طلب استرجاع',
            self::REQUEST_REJECTED => 'تم رفض طلب الاسترجاع',
            self::REFUNDED => 'تم الاسترجاع',
        };
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::NEW => 'info',
            self::CONFIRMED => 'primary',
            self::OUT_OF_DELIVERY => 'primary',
            self::DELIVERED => 'success',
            self::PROBLEM => 'danger',
            self::CANCELLED => 'danger',
            self::REQUEST_CANCEL => 'warning',
            self::REQUEST_REFUND => 'warning',
            self::REQUEST_REJECTED => 'danger',
            self::REFUNDED => 'secondary',
        };
    }

    /**
     * Get status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::PROCESSING => 'box',
            self::NEW => 'user-check',
            self::CONFIRMED => 'check',
            self::OUT_OF_DELIVERY => 'truck',
            self::DELIVERED => 'truck',
            self::PROBLEM => 'alert-triangle',
            self::CANCELLED => 'x-circle',
            self::REQUEST_CANCEL => 'x-circle',
            self::REQUEST_REFUND => 'rotate-ccw',
            self::REQUEST_REJECTED => 'x-circle',
            self::REFUNDED => 'dollar-sign',
        };
    }

    /**
     * Check if status can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::PROCESSING,
            self::NEW,
            self::CONFIRMED,
            self::PROBLEM,
        ]);
    }

    /**
     * Check if status is final
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::DELIVERED,
            self::CANCELLED,
            self::REFUNDED,
        ]);
    }

    /**
     * Get next possible statuses based on workflow
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::NEW, self::CANCELLED],
            self::PROCESSING => [self::CONFIRMED, self::CANCELLED, self::PROBLEM],
            self::NEW => [self::OUT_OF_DELIVERY, self::CONFIRMED, self::CANCELLED, self::PROBLEM],
            self::CONFIRMED => [self::OUT_OF_DELIVERY, self::DELIVERED, self::CANCELLED, self::PROBLEM],
            self::DELIVERED => [self::REQUEST_REFUND],
            self::PROBLEM => [self::CANCELLED, self::NEW],
            self::CANCELLED => [self::REFUNDED],
            self::REQUEST_CANCEL => [self::CANCELLED],
            self::REQUEST_REFUND => [self::REFUNDED, self::REQUEST_REJECTED, self::CANCELLED],
            self::REFUNDED => [],
        };
    }

    /**
     * Check if admin/manager can update to this status
     */
    public function canBeSetByAdmin(): bool
    {
        return true; // Admin can set any status
    }

    /**
     * Check if branch manager can update to this status
     */
    public function canBeSetByBranchManager(): bool
    {
        return in_array($this, [
            self::NEW,
            self::CONFIRMED,
            self::DELIVERED,
            self::CANCELLED,
        ]);
    }

    /**
     * Check if delivery person can update to this status
     */
    public function canBeSetByDelivery(): bool
    {
        return in_array($this, [
            self::CONFIRMED,
            self::OUT_OF_DELIVERY,
            self::DELIVERED,
        ]);
    }
}

