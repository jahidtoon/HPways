<?php

return [
    'enabled' => env('TRACKING_ENABLED', true),

    'fedex' => [
        'api_key' => env('FEDEX_API_KEY'),
        'api_secret' => env('FEDEX_API_SECRET'),
        'account_number' => env('FEDEX_ACCOUNT_NUMBER'),
    ],

    'dhl' => [
        'api_key' => env('DHL_API_KEY'),
    ],

    'ups' => [
        'access_license' => env('UPS_ACCESS_LICENSE'),
        'username' => env('UPS_USERNAME'),
        'password' => env('UPS_PASSWORD'),
    ],

    'usps' => [
        'user_id' => env('USPS_USER_ID'),
    ],
];
