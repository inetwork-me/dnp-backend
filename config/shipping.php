<?php

return [
    'aramex' => [
        'username' => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
        'password' => env('ARAMEX_PASSWORD', 'R123456789$r'),
        'account_number' => env('ARAMEX_ACCOUNT_NUMBER', '987654'),
        'account_pin' => env('ARAMEX_ACCOUNT_PIN', '226321'),
        'account_entity' => env('ARAMEX_ACCOUNT_ENTITY', 'CAI'),
        'account_country_code' => env('ARAMEX_ACCOUNT_COUNTRY', 'EG'),
        'is_production' => env('ARAMEX_PRODUCTION', false),
        'shipper_phone' => env('ARAMEX_SHIPPER_PHONE', '201000000000'),
        'shipper_email' => env('ARAMEX_SHIPPER_EMAIL', 'orders@dnpstore.com'),
    ],
];
