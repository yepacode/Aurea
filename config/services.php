<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'epayco' => [
        'public_key'  => env('EPAYCO_PUBLIC_KEY'),
        'private_key' => env('EPAYCO_PRIVATE_KEY'),
        'p_cust_id'   => env('EPAYCO_P_CUST_ID'),   // P_CUST_ID_CLIENTE
        'p_key'       => env('EPAYCO_P_KEY'),        // P_KEY (para validar la firma del webhook)
        'test'        => env('EPAYCO_TEST', true),   // true = sandbox
        'currency'    => env('EPAYCO_CURRENCY', 'cop'),
    ],

    // Web Push (VAPID): se usan para firmar los pushes y para que el
    // navegador reconozca al servidor. Las llaves se generan una sola vez
    // con `php artisan push:generate-keys` y se pegan en .env.
    'webpush' => [
        'vapid' => [
            'subject'     => env('VAPID_SUBJECT', 'mailto:hola@bellezaaurea.com'),
            'public_key'  => env('VAPID_PUBLIC_KEY'),
            'private_key' => env('VAPID_PRIVATE_KEY'),
        ],
    ],

    // Analytics: se pisan desde la BD (AnalyticsSetting) si el admin los captura
    // desde el panel; si están vacíos, no se imprime nada en el HTML.
    'analytics' => [
        'ga4'        => env('GA4_MEASUREMENT_ID'),   // Formato G-XXXXXXXXXX
        'meta_pixel' => env('META_PIXEL_ID'),        // 15–16 dígitos
    ],

];
