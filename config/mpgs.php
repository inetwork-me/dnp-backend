<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MPGS Environment
    |--------------------------------------------------------------------------
    | Set to true for test mode, false for production
    */
    'use_test' => env('MPGS_USE_TEST', true),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    */
    'api_version' => env('MPGS_API_VERSION', '100'),

    /*
    |--------------------------------------------------------------------------
    | Production Credentials
    |--------------------------------------------------------------------------
    */
    'production' => [
        'merchant_id' => env('MPGS_PROD_MERCHANT_ID'),
        'username' => env('MPGS_PROD_USERNAME'),
        'password' => env('MPGS_PROD_PASSWORD'),
        'api_url' => env('MPGS_PROD_API_URL', 'https://cibpaynow.gateway.mastercard.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Credentials
    |--------------------------------------------------------------------------
    */
    'test' => [
        'merchant_id' => env('MPGS_TEST_MERCHANT_ID'),
        'username' => env('MPGS_TEST_USERNAME'),
        'password' => env('MPGS_TEST_PASSWORD'),
        'api_url' => env('MPGS_TEST_API_URL', 'https://test-cibpaynow.gateway.mastercard.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend URL for Redirects
    |--------------------------------------------------------------------------
    */
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];
