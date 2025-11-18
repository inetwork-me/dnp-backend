<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Aramex Shipping Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration supports both test and production credentials.
    | Set ARAMEX_USE_TEST=true in .env to use test credentials.
    | Otherwise, it will use production credentials from .env or defaults below.
    |
    */

    'aramex' => [
        // Determine if we should use test credentials
        'use_test_mode' => env('ARAMEX_USE_TEST', true),

        // Production credentials (default)
        'production' => [
            'username' => env('ARAMEX_PROD_USERNAME', 'customer.service@dnpeg.com'),
            'password' => env('ARAMEX_PROD_PASSWORD', 'Reemdnpeg2024@'),
            'account_number' => env('ARAMEX_PROD_ACCOUNT_NUMBER', '72511422'),
            'account_pin' => env('ARAMEX_PROD_ACCOUNT_PIN', '597139'),
            'account_entity' => env('ARAMEX_PROD_ACCOUNT_ENTITY', 'CAI'),
            'account_country_code' => env('ARAMEX_PROD_ACCOUNT_COUNTRY', 'EG'),
            'shipper_phone' => env('ARAMEX_PROD_SHIPPER_PHONE', '201000000000'),
            'shipper_email' => env('ARAMEX_PROD_SHIPPER_EMAIL', 'customer.service@dnpeg.com'),
        ],

        // Test credentials (for development/staging)
        'test' => [
            'username' => env('ARAMEX_TEST_USERNAME', 'testingapi@aramex.com'),
            'password' => env('ARAMEX_TEST_PASSWORD', 'R123456789$r'),
            'account_number' => env('ARAMEX_TEST_ACCOUNT_NUMBER', '987654'),
            'account_pin' => env('ARAMEX_TEST_ACCOUNT_PIN', '226321'),
            'account_entity' => env('ARAMEX_TEST_ACCOUNT_ENTITY', 'CAI'),
            'account_country_code' => env('ARAMEX_TEST_ACCOUNT_COUNTRY', 'EG'),
            'shipper_phone' => env('ARAMEX_TEST_SHIPPER_PHONE', '201000000000'),
            'shipper_email' => env('ARAMEX_TEST_SHIPPER_EMAIL', 'orders@dnpstore.com'),
        ],

        // Active credentials (auto-selected based on use_test_mode)
        'username' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_USERNAME', 'testingapi@aramex.com')
            : env('ARAMEX_PROD_USERNAME', 'customer.service@dnpeg.com'),

        'password' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_PASSWORD', 'R123456789$r')
            : env('ARAMEX_PROD_PASSWORD', 'Reemdnpeg2024@'),

        'account_number' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_ACCOUNT_NUMBER', '987654')
            : env('ARAMEX_PROD_ACCOUNT_NUMBER', '72511422'),

        'account_pin' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_ACCOUNT_PIN', '226321')
            : env('ARAMEX_PROD_ACCOUNT_PIN', '597139'),

        'account_entity' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_ACCOUNT_ENTITY', 'CAI')
            : env('ARAMEX_PROD_ACCOUNT_ENTITY', 'CAI'),

        'account_country_code' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_ACCOUNT_COUNTRY', 'EG')
            : env('ARAMEX_PROD_ACCOUNT_COUNTRY', 'EG'),

        'shipper_phone' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_SHIPPER_PHONE', '201000000000')
            : env('ARAMEX_PROD_SHIPPER_PHONE', '201000000000'),

        'shipper_email' => env('ARAMEX_USE_TEST', false)
            ? env('ARAMEX_TEST_SHIPPER_EMAIL', 'orders@dnpstore.com')
            : env('ARAMEX_PROD_SHIPPER_EMAIL', 'customer.service@dnpeg.com'),

        // Production mode flag (affects Aramex API endpoint)
        'is_production' => !env('ARAMEX_USE_TEST', false),
    ],
];