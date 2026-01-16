<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\SiteSetting;

class FeeCalculationService
{
    /**
     * Calculate all fees for a cart
     *
     * @param Cart $cart
     * @param array $options - Additional options like booking_type, delivery_type
     * @return array
     */
    public function calculateCartFees(Cart $cart, array $options = [])
    {
        $fees = [
            'booking_fee' => 0,
            'home_service_fee' => 0,
            'delivery_fee' => 0,
            'total_fees' => 0,
        ];

        // Get fee settings
        $settings = $this->getFeeSettings();

        // Calculate booking fee for services
        if ($cart->hasServices()) {
            $fees['booking_fee'] = $this->calculateBookingFee($cart, $settings);

            // Calculate home service fee if booking type is 'home'
            if (isset($options['booking_type']) && $options['booking_type'] === 'home') {
                $fees['home_service_fee'] = $this->calculateHomeServiceFee($cart, $settings);
            }
        }

        // Calculate delivery fee for products
        if ($cart->hasProducts()) {
            $deliveryType = $options['delivery_type'] ?? 'normal';
            $fees['delivery_fee'] = $this->calculateDeliveryFee($cart, $deliveryType, $settings);
        }

        $fees['total_fees'] = $fees['booking_fee'] + $fees['home_service_fee'] + $fees['delivery_fee'];

        return $fees;
    }

    /**
     * Calculate booking fee for services
     * رسوم الحجز - Added when requesting services, set by admin, refundable on cancellation
     *
     * @param Cart $cart
     * @param array $settings
     * @return float
     */
    private function calculateBookingFee(Cart $cart, array $settings)
    {
        if (!$cart->hasServices()) {
            return 0;
        }

        // Fixed booking fee set by admin
        return (float) ($settings['booking_fee_amount'] ?? 10.00);
    }

    /**
     * Calculate home service fee
     * رسوم بالمنزل - Added when choosing home location for services, set by service provider, refundable on cancellation
     *
     * @param Cart $cart
     * @param array $settings
     * @return float
     */
    private function calculateHomeServiceFee(Cart $cart, array $settings)
    {
        if (!$cart->hasServices() || !$cart->provider) {
            return 0;
        }

        // Check if provider offers home services
        if (!$cart->provider->in_home) {
            return 0;
        }

        // Use provider's home service fee if set, otherwise use default
        $providerHomeFee = $cart->provider->home_fees ?? 0;
        $defaultHomeFee = (float) ($settings['default_home_service_fee'] ?? 15.00);

        return $providerHomeFee > 0 ? (float) $providerHomeFee : $defaultHomeFee;
    }

    /**
     * Calculate delivery fee for products
     * رسوم التوصيل - Added when requesting products, set by admin, refundable on cancellation
     *
     * @param Cart $cart
     * @param string $deliveryType
     * @param array $settings
     * @return float
     */
    private function calculateDeliveryFee(Cart $cart, string $deliveryType, array $settings)
    {
        if (!$cart->hasProducts()) {
            return 0;
        }

        $normalDeliveryFee = (float) ($settings['normal_delivery_fee'] ?? 5.00);
        $expressDeliveryFee = (float) ($settings['express_delivery_fee'] ?? 15.00);

        return $deliveryType === 'express' ? $expressDeliveryFee : $normalDeliveryFee;
    }

    /**
     * Calculate cancellation fee
     * رسوم الإلغاء - Deducted when customer sends cancellation request and admin accepts it
     *
     * @param Order $order
     * @return array
     */
    public function calculateCancellationFee(Order $order)
    {
        $settings = $this->getFeeSettings();

        // Calculate refundable amount (original total minus cancellation fee)
        $cancellationFeeAmount = (float) ($settings['cancellation_fee_amount'] ?? 5.00);
        $cancellationFeePercentage = (float) ($settings['cancellation_fee_percentage'] ?? 0);

        // Calculate fee based on percentage if set, otherwise use fixed amount
        $cancellationFee = 0;
        if ($cancellationFeePercentage > 0) {
            $cancellationFee = ($order->total * $cancellationFeePercentage) / 100;
        } else {
            $cancellationFee = $cancellationFeeAmount;
        }

        // Ensure cancellation fee doesn't exceed the order total
        $cancellationFee = min($cancellationFee, $order->total);

        $refundableAmount = $order->total - $cancellationFee;

        return [
            'cancellation_fee' => $cancellationFee,
            'refundable_amount' => max(0, $refundableAmount),
            'original_total' => $order->total,
        ];
    }

    /**
     * Get fee settings from database
     *
     * @return array
     */
    private function getFeeSettings()
    {
        $appInfo = SiteSetting::pluck('value', 'key')->toArray();

        return [
            // Booking fee settings
            'booking_fee_amount' => $appInfo['booking_fee_amount'] ?? 10.00,

            // Home service fee settings
            'default_home_service_fee' => $appInfo['default_home_service_fee'] ?? 15.00,

            // Delivery fee settings
            'normal_delivery_fee' => $appInfo['normal_delivery_fee'] ?? 5.00,
            'express_delivery_fee' => $appInfo['express_delivery_fee'] ?? 15.00,

            // Cancellation fee settings
            'cancellation_fee_amount' => $appInfo['cancellation_fee_amount'] ?? 5.00,
            'cancellation_fee_percentage' => $appInfo['cancellation_fee_percentage'] ?? 0, // 0 means use fixed amount
        ];
    }

    /**
     * Update cart with calculated fees
     *
     * @param Cart $cart
     * @param array $options
     * @return Cart
     */
    public function updateCartFees(Cart $cart, array $options = [])
    {
        $fees = $this->calculateCartFees($cart, $options);

        $cart->update([
            'booking_fee' => $fees['booking_fee'],
            'home_service_fee' => $fees['home_service_fee'],
            'delivery_fee' => $fees['delivery_fee'],
        ]);

        return $cart;
    }

    /**
     * Get fee breakdown for display
     *
     * @param Cart $cart
     * @param array $options
     * @return array
     */
    public function getFeeBreakdown(Cart $cart, array $options = [])
    {
        $fees = $this->calculateCartFees($cart, $options);
        $settings = $this->getFeeSettings();

        $breakdown = [];

        if ($fees['booking_fee'] > 0) {
            $breakdown[] = [
                'type' => 'booking_fee',
                'name_ar' => 'رسوم الحجز',
                'name_en' => 'Booking Fee',
                'amount' => $fees['booking_fee'],
                'refundable' => true,
                'description_ar' => 'رسوم إضافية للحجز تحدد من قبل الإدارة',
                'description_en' => 'Additional booking fee set by administration',
            ];
        }

        if ($fees['home_service_fee'] > 0) {
            $breakdown[] = [
                'type' => 'home_service_fee',
                'name_ar' => 'رسوم الخدمة بالمنزل',
                'name_en' => 'Home Service Fee',
                'amount' => $fees['home_service_fee'],
                'refundable' => true,
                'description_ar' => 'رسوم إضافية لتنفيذ الخدمة بالمنزل',
                'description_en' => 'Additional fee for home service execution',
            ];
        }

        if ($fees['delivery_fee'] > 0) {
            $deliveryType = $options['delivery_type'] ?? 'normal';
            $breakdown[] = [
                'type' => 'delivery_fee',
                'name_ar' => $deliveryType === 'express' ? 'رسوم التوصيل السريع' : 'رسوم التوصيل',
                'name_en' => $deliveryType === 'express' ? 'Express Delivery Fee' : 'Delivery Fee',
                'amount' => $fees['delivery_fee'],
                'refundable' => true,
                'description_ar' => 'رسوم توصيل المنتجات تحدد من قبل الإدارة',
                'description_en' => 'Product delivery fee set by administration',
            ];
        }

        return $breakdown;
    }
}
