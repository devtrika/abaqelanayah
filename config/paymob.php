<?php

$integrationIds = env('PAYMOB_INTEGRATION_ID', '');

$integrationIdsArray = $integrationIds !== ''
    ? array_map('intval', explode(',', $integrationIds))
    : [];

return [
    'api_key' => env('PAYMOB_API_KEY'),
    'secret_key' => env('PAYMOB_SECRET_KEY'),
    'public_key' => env('PAYMOB_PUBLIC_KEY'),
    'iframe_id' => env('PAYMOB_IFRAME_ID'),
    'currency' => env('PAYMOB_CURRENCY', 'SAR'),

    // Card integration id (first one)
    'card_integration_id' => $integrationIdsArray[0] ?? 0,

    // Card integration ids (array)
    'card_integration_ids' => $integrationIdsArray,

    // Optional wallet integration id
    'wallet_integration_id' => (int) env('PAYMOB_WALLET_INTEGRATION_ID', 0),

    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
    'base_url' => env('PAYMOB_BASE_URL', 'https://ksa.paymob.com'),
    'notification_url' => env('PAYMOB_NOTIFICATION_URL'),
    'redirection_url' => env('PAYMOB_REDIRECTION_URL'),

    // Skip HMAC verification for webhook/callback if needed
    'skip_verification' => (bool) env('PAYMOB_SKIP_VERIFICATION', true),

    // Map internal payment method IDs to Paymob Integration IDs
    'payment_method_map' => [
        1 => env('PAYMOB_CARD_INTEGRATION_ID'),
        2 => env('PAYMOB_CARD_INTEGRATION_ID'),
        4 => env('PAYMOB_CARD_INTEGRATION_ID'),
        3 => env('PAYMOB_APPLE_PAY_INTEGRATION_ID'),
    ],
];
