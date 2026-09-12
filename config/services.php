<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cloudflare-analytics' => [
        'token' => env('CLOUDFLARE_ANALYTICS_TOKEN'),
    ],

    'legacy' => [
        // Grants read access to the legacy app's filemanager bridge routes
        // during the pages import; must match the legacy app's value.
        'export_token' => env('LEGACY_EXPORT_TOKEN'),
    ],

    'mailcoach' => [
        'url' => env('MAILCOACH_URL'),
        'token' => env('MAILCOACH_TOKEN'),
        'lists' => [
            'default' => env('MAILCOACH_DEFAULT_LIST_UUID'),
        ],
    ],

];
