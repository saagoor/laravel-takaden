<?php

return [
    'bkash' => [
        'base_url'      => env('BKASH_BASE_URL', 'https://checkout.sandbox.bka.sh/v1.2.0-beta'),
        'script_url'    => env('BKASH_BASE_URL', 'https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js'),
        'intent'        => env('BKASH_BASE_URL', 'sale'),
        'app_key'       => env('BKASH_APP_KEY'),
        'app_secret'    => env('BKASH_APP_SECRET'),
        'username'      => env('BKASH_USERNAME', 'sandboxTestUser'),
        'password'      => env('BKASH_PASSWORD', 'hWD@8vtzw0'),
    ],
];
