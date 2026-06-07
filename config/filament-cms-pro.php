<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Editorial Approval Workflow
    |--------------------------------------------------------------------------
    |
    | When enabled (true), all posts must transition through the gated states:
    | Draft -> Review -> Fact Check -> Editor Approval -> Publisher Approval.
    | Set to false to allow direct publishing from draft state.
    |
    */
    'workflow' => [
        'enabled' => true,
        'slack_webhook_url' => env('CMS_SLACK_WEBHOOK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Non-Intrusive Server-Side Analytics
    |--------------------------------------------------------------------------
    |
    | Track page views and unique users on the server side without injecting
    | heavy tracking scripts. GDPR compliant, cookie-less fingerprinting.
    |
    */
    'analytics' => [
        'enabled' => true,
        'gravity' => 1.8, // Gravity decay parameter for trending calculation
        'exclude_paths' => [
            'admin*',
            'api/v1/analytics*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Comments System Settings
    |--------------------------------------------------------------------------
    |
    | Configure comment moderation and spam detection thresholds.
    |
    */
    'comments' => [
        'auto_approve' => false,
        'spam_check' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Strategies
    |--------------------------------------------------------------------------
    |
    | Configure redis caching parameters for rendered articles.
    |
    */
    'cache' => [
        'ttl' => 86400, // 24 hours caching for public post compilation
    ],
];
