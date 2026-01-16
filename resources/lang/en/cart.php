<?php

return [
    // Cart general messages
    'cart_is_empty' => 'Cart is empty',
    'invalid_item_type' => 'Invalid item type',
    
    // Product messages
    'product_not_available' => 'Product is not available',
    'insufficient_stock' => 'Insufficient stock',
    
    // Service messages
    'service_not_available' => 'Service is not available',
    'service_provider_unavailable' => 'Service provider is currently unavailable. Please try again later',
    'service_already_in_cart' => 'Service already exists in cart',
    'service_quantity_cannot_be_updated' => 'Service quantity cannot be updated',
    
    // Coupon messages
    'invalid_coupon_code' => 'Invalid coupon code',
    'coupon_not_valid_or_usage_limit_reached' => 'Coupon is not valid or has reached usage limit',
    'coupon_only_valid_for_provider' => 'This coupon is only valid for :provider',
    'coupon_not_applicable_to_cart_services' => 'This coupon is not applicable to the services in your cart',
    'minimum_order_value_required' => 'Minimum order value of :amount :currency required to use this coupon',
    'coupon_already_applied' => 'Coupon already applied',
    'no_coupon_applied' => 'No coupon applied to cart',
    'coupon_already_used' => 'You have already used this coupon.',
    'currency' => 'SAR',

    // Loyalty points messages
    'loyalty_points_system_disabled' => 'Loyalty points system is disabled',
    'minimum_points_required' => 'Minimum :points points required to redeem',
    'insufficient_loyalty_points' => 'Insufficient loyalty points',
    'cannot_use_more_than_max_points' => 'Cannot use more than :max_points points (:percentage% of cart total)',
    
    // Cart conflict messages
    'product_different_provider_with_services_conflict' => 'Adding this product will remove all current services from your cart as they are from a different provider. Do you want to continue?',
    'product_different_provider_with_products_conflict' => 'Adding this product will remove all current products from your cart as they are from a different provider. Do you want to continue?',
    'service_different_provider_conflict' => 'Adding this service will remove all current services from your cart as they are from a different provider. Do you want to continue?',
    'service_with_multiple_products_conflict' => 'Adding this service will remove all current products from your cart as services require a single provider. Do you want to continue?',
    'service_different_provider_with_products_conflict' => 'Adding this service will remove all current products from your cart as they are from a different provider. Do you want to continue?',

    // General error messages
    'not_found' => 'Not found',
    'error_occurred' => 'An error occurred',
     'coupon_not_available' => 'This coupon is not available.',
    'coupon_not_found' => 'This coupon does not exist.',
    'coupon_usage_ended'   => 'This coupon can no longer be used.',
    'coupon_expired'       => 'This coupon has expired.',
    'coupon_not_started'   => 'This coupon is not valid yet.',
    'invalid_coupon_data'  => 'Invalid coupon data.',
        'insufficient_wallet_balance' => 'Insufficient wallet balance',
    'maximum_wallet_deduction_exceeded' => 'Maximum wallet deduction of :amount  exceeded',
    'requested_quantity_exceeds_available' => 'The requested quantity exceeds available stock for this product.',
];
