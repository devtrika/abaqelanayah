<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | This option defines the default notification channels that will be used
    | when sending notifications. You can override this on a per-notification
    | basis by specifying channels when creating notifications.
    |
    */

    'default_channels' => [
        'database',
        'firebase',
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure settings for each notification channel.
    |
    */

    'channels' => [
        'database' => [
            'enabled' => true,
        ],

        'firebase' => [
            'enabled' => env('FIREBASE_ENABLED', true),
            'server_key' => env('FIREBASE_SERVER_KEY'),
            'sender_id' => env('FIREBASE_SENDER_ID'),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'api_key' => env('FIREBASE_API_KEY'),
            'timeout' => 30,
            'retry_attempts' => 3,
            'retry_delay' => 1, // seconds
        ],

        'mail' => [
            'enabled' => env('MAIL_NOTIFICATIONS_ENABLED', true),
        ],

        'sms' => [
            'enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how notifications should be queued for processing.
    |
    */

    'queue' => [
        'enabled' => env('NOTIFICATION_QUEUE_ENABLED', true),
        'connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'redis'),
        'queue' => env('NOTIFICATION_QUEUE_NAME', 'notifications'),
        'delay' => env('NOTIFICATION_QUEUE_DELAY', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | Define the available notification types and their default settings.
    |
    */

    'types' => [
        'order_status' => [
            'class' => \App\Notifications\OrderStatusNotification::class,
            'channels' => ['database', 'firebase'],
            'priority' => 'normal',
            'queue' => true,
        ],

        'refund_status' => [
            'class' => \App\Notifications\RefundStatusNotification::class,
            'channels' => ['database', 'firebase', 'mail'],
            'priority' => 'high',
            'queue' => true,
        ],

        'system' => [
            'class' => \App\Notifications\SystemNotification::class,
            'channels' => ['database', 'firebase'],
            'priority' => 'normal',
            'queue' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Firebase Cloud Messaging.
    |
    */

    'firebase' => [
        'fcm_url' => 'https://fcm.googleapis.com/fcm/send',
        'default_sound' => 'default',
        'default_icon' => '/favicon.ico',
        'default_color' => '#2196F3',
        
        'android' => [
            'channels' => [
                'default' => [
                    'name' => 'Default',
                    'description' => 'Default notifications',
                    'importance' => 'high',
                ],
                'order_updates' => [
                    'name' => 'Order Updates',
                    'description' => 'Notifications about order status changes',
                    'importance' => 'high',
                ],
                'refund_updates' => [
                    'name' => 'Refund Updates',
                    'description' => 'Notifications about refund status changes',
                    'importance' => 'high',
                ],
                'system_general' => [
                    'name' => 'System Notifications',
                    'description' => 'General system notifications',
                    'importance' => 'normal',
                ],
                'system_maintenance' => [
                    'name' => 'Maintenance Alerts',
                    'description' => 'System maintenance notifications',
                    'importance' => 'high',
                ],
                'promotions' => [
                    'name' => 'Promotions',
                    'description' => 'Promotional offers and deals',
                    'importance' => 'low',
                ],
                'app_updates' => [
                    'name' => 'App Updates',
                    'description' => 'Application update notifications',
                    'importance' => 'normal',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of old notifications and device tokens.
    |
    */

    'cleanup' => [
        'enabled' => env('NOTIFICATION_CLEANUP_ENABLED', true),
        'read_notifications_days' => env('NOTIFICATION_CLEANUP_READ_DAYS', 30),
        'unread_notifications_days' => env('NOTIFICATION_CLEANUP_UNREAD_DAYS', 90),
        'inactive_device_tokens_days' => env('NOTIFICATION_CLEANUP_TOKENS_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for notifications to prevent spam.
    |
    */

    'rate_limiting' => [
        'enabled' => env('NOTIFICATION_RATE_LIMITING_ENABLED', true),
        'max_per_user_per_hour' => env('NOTIFICATION_RATE_LIMIT_HOURLY', 50),
        'max_per_user_per_day' => env('NOTIFICATION_RATE_LIMIT_DAILY', 200),
        'max_firebase_per_minute' => env('FIREBASE_RATE_LIMIT_MINUTE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Configure fallback behavior when primary channels fail.
    |
    */

    'fallback' => [
        'enabled' => true,
        'channels' => [
            'firebase' => ['database'], // If Firebase fails, fallback to database
            'mail' => ['database'],     // If mail fails, fallback to database
            'sms' => ['database'],      // If SMS fails, fallback to database
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for notification events.
    |
    */

    'logging' => [
        'enabled' => env('NOTIFICATION_LOGGING_ENABLED', true),
        'level' => env('NOTIFICATION_LOG_LEVEL', 'info'),
        'log_successful_sends' => env('NOTIFICATION_LOG_SUCCESS', false),
        'log_failed_sends' => env('NOTIFICATION_LOG_FAILURES', true),
        'log_channel' => env('NOTIFICATION_LOG_CHANNEL', 'daily'),
    ],

];
