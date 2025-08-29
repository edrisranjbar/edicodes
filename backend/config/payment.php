<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for various payment gateways
    | supported by the shetabit/payment package.
    |
    */

    'default' => env('PAYMENT_DEFAULT', 'zarinpal'),

    'gateways' => [
        'zarinpal' => [
            'merchantId' => env('ZARINPAL_MERCHANT_ID', ''),
            'callbackUrl' => env('ZARINPAL_CALLBACK_URL', ''),
            'sandbox' => env('ZARINPAL_SANDBOX', true),
        ],
        
        'nextpay' => [
            'apiKey' => env('NEXTPAY_API_KEY', ''),
            'callbackUrl' => env('NEXTPAY_CALLBACK_URL', ''),
            'sandbox' => env('NEXTPAY_SANDBOX', true),
        ],
        
        'parsijoo' => [
            'merchantId' => env('PARSIJOO_MERCHANT_ID', ''),
            'callbackUrl' => env('PARSIJOO_CALLBACK_URL', ''),
            'sandbox' => env('PARSIJOO_SANDBOX', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | General payment settings and defaults
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'Toman'),
    
    'currency_multiplier' => env('PAYMENT_CURRENCY_MULTIPLIER', 10), // 1 Toman = 10 Rials
    
    'timeout' => env('PAYMENT_TIMEOUT', 300), // 5 minutes
    
    'retry_attempts' => env('PAYMENT_RETRY_ATTEMPTS', 3),
    
    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    |
    | Settings related to payment refunds
    |
    */

    'refund' => [
        'allowed_hours' => env('PAYMENT_REFUND_HOURS', 24),
        'require_reason' => env('PAYMENT_REFUND_REQUIRE_REASON', true),
        'auto_approve_free' => env('PAYMENT_REFUND_AUTO_APPROVE_FREE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Settings for payment notifications
    |
    */

    'notifications' => [
        'email' => [
            'enabled' => env('PAYMENT_EMAIL_NOTIFICATIONS', true),
            'admin_email' => env('PAYMENT_ADMIN_EMAIL', 'admin@example.com'),
        ],
        'sms' => [
            'enabled' => env('PAYMENT_SMS_NOTIFICATIONS', false),
            'provider' => env('PAYMENT_SMS_PROVIDER', 'kavenegar'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Settings for payment logging
    |
    */

    'logging' => [
        'enabled' => env('PAYMENT_LOGGING', true),
        'level' => env('PAYMENT_LOG_LEVEL', 'info'),
        'channel' => env('PAYMENT_LOG_CHANNEL', 'payment'),
    ],
];
