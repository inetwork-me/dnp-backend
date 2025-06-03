<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // 1) Match any URI that contains "/api/". The leading/trailing "*" let
    //    Laravel apply CORS rules no matter which subfolder (/dnp-backend/…)
    'paths' => [
        '*api/*',
        '*/sanctum/csrf-cookie',
    ],

    // 2) Allow all HTTP methods (GET, POST, PUT, PATCH, DELETE, OPTIONS, etc.)
    'allowed_methods' => ['*'],

    // 3) Only open CORS for our Next.js dev origin
    //    In .env (development), set ALLOW_CORS_ORIGIN=http://localhost:3000
    //    In production, leave ALLOW_CORS_ORIGIN empty or set to your real domain.
    'allowed_origins' => explode(',', env('ALLOW_CORS_ORIGIN', '')),

    'allowed_origins_patterns' => [],

    // 4) Permit any headers the client might send
    'allowed_headers' => ['*'],

    // 5) No special “exposed” headers needed in development
    'exposed_headers' => [],

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Whether to allow credentials (cookies, authorization headers) during CORS
    |--------------------------------------------------------------------------
    |
    | We only enable “credentials” if ALLOW_CORS_ORIGIN is non-empty.
    | In dev, ALLOW_CORS_ORIGIN=http://localhost:3000 → supports_credentials = true
    | In prod, ALLOW_CORS_ORIGIN is empty → supports_credentials = false
    |
    */
    'supports_credentials' => env('ALLOW_CORS_ORIGIN') ? true : false,
];
