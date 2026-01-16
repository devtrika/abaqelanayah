<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MyFatoorah Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for MyFatoorah payment gateway
    |
    */

    // API Configuration
'api_key' => env('MYFATOORAH_API_KEY', 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL'),

    'test_mode' => env('MYFATOORAH_TEST_MODE', true),

    'webhook_secret' => env('MYFATOORAH_WEBHOOK_SECRET', 'Mf02IoVHFMDLaGWgi5WKteIzVGsY80WpZ4GzQfUb54Eqz48x2YigJukAvEsagzDOQDOm0XSDbWsQZv5JWtkPsA=='),

    // Default Settings
    'currency' => env('MYFATOORAH_CURRENCY', 'SAR'),
    'country_code' => env('MYFATOORAH_COUNTRY_CODE', '+966'),
    'language' => env('MYFATOORAH_LANGUAGE', 'ar'),
    'region' => env('MYFATOORAH_REGION', 'SAU'), // SAU for Saudi Arabia, KWT for Kuwait, etc.
    'notification_option' => env('MYFATOORAH_NOTIFICATION_OPTION', 'Lnk'), // Lnk, SMS, EML, or ALL

    // Callback URLs
    'success_url' => env('MYFATOORAH_SUCCESS_URL', '/payment/success'),
    'error_url' => env('MYFATOORAH_ERROR_URL', '/payment/error'),
    'webhook_url' => env('MYFATOORAH_WEBHOOK_URL', '/payment/callback'),

    // Logging
    'log_enabled' => env('MYFATOORAH_LOG_ENABLED', true),
    'log_file' => env('MYFATOORAH_LOG_FILE', storage_path('logs/myfatoorah.log')),

    // Payment Methods
    'payment_methods' => [
        'myfatoorah' => 'myfatoorah',
        'knet' => 'kn',
        'visa_master' => 'vm',
        'amex' => 'ae',
        'sadad' => 'sd',
        'apple_pay' => 'ap',
        'stc_pay' => 'stcpay',
    ],

    // Supported Gateways for Course Payments
    'course_gateways' => [
        'myfatoorah',
        'knet',
        'visa_master',
        'amex',
        'sadad',
        'apple_pay',
        'stc_pay',
    ],
];
