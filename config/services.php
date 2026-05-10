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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'weather' => [
        'key' => env('WEATHER_API'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'eur'),
    ],

    /*
    | PayPal: demo keskkonnas kasutatakse serveripoolset simulatsiooni (nagu RalfiHarjutus).
    | Tootmises asenda päris REST API (Orders v2 + OAuth) ja CAPTURE tagasisidega.
    */
    'paypal' => [
        'simulate_failure' => filter_var(env('PAYPAL_SIMULATE_FAILURE', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    | Sõbra avalik API (nt filmide demo) — päring proxy'takse läbi meie /api/friend-favorite-subjects,
    | et vältida brauseri CORS piiranguid ja ühtlustada vastuse kuju NFL kaartide jaoks.
    */
    'friend_subjects' => [
        'url' => env(
            'FRIEND_SUBJECTS_API_URL',
            'https://raamistikud.ta24armus.itmajakas.ee/api/my-favorite-subjects'
        ),
    ],
];
