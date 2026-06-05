<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Mobile (Flutter) clients hit /api/* for JSON and /storage/* for
    | avatar images. Both paths need permissive CORS so requests from
    | the mobile WebView / Image.network() succeed.
    |
    | If you tighten origins for production, keep `storage/*` allowed for
    | any origin that should be able to display user images.
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'storage/*',
        'login',
        'logout',
        'register',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
