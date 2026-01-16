<?php

return [
    'base_url' => env('UNIFONIC_BASE_URL', 'https://apis.unifonic.com'),
    'public_id' => env('UNIFONIC_PUBLIC_ID', ''),
    'secret' => env('UNIFONIC_SECRET', ''),
    'whatsapp' => [
        'enabled' => env('UNIFONIC_WHATSAPP_ENABLED', false),
        'messages_endpoint' => '/v1/messages',
        'template' => env('UNIFONIC_WHATSAPP_TEMPLATE', 'auth_23april_abd'),
        'language' => env('UNIFONIC_WHATSAPP_LANGUAGE', 'en'),
        'webhook_secret' => env('UNIFONIC_WHATSAPP_WEBHOOK_SECRET', ''),
    ],
];

