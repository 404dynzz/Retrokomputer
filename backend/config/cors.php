<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your Laravel application.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'https://retrokomputer.vercel.app',
        // Allow all IPs on local network for mobile testing
        'http://192.168.*',
        'http://10.*',
    ],

    'allowed_origins_patterns' => [
        '#^http://192\.168\..+:5173$#',
        '#^http://10\..+:5173$#',
        '#^http://192\.168\..+:8000$#',
        '#^http://10\..+:8000$#',
        '#^https://.+\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
