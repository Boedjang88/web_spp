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

    'pddikti' => [
        'url' => env('PDDIKTI_FEEDER_URL', 'http://127.0.0.1:8082/ws/live2.php'),
        'username' => env('PDDIKTI_FEEDER_USERNAME'),
        'password' => env('PDDIKTI_FEEDER_PASSWORD'),
        'verify_ssl' => env('PDDIKTI_FEEDER_VERIFY_SSL', true),
        'sandbox' => env('PDDIKTI_FEEDER_SANDBOX', false),
    ],

    'whatsapp' => [
        'url' => env('WHATSAPP_API_URL', 'https://api.whatsapp-gateway.campus.ac.id/send'),
        'key' => env('WHATSAPP_API_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://127.0.0.1:8000/auth/google/callback'),
    ],

];
