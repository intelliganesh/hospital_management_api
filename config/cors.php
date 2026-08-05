<?php

return [

    'paths'                    => ['*'], // ✅ Allow CORS on all endpoints

    'allowed_methods'          => ['*'], // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)

    'allowed_origins'          => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:5174',
        'http://127.0.0.1:5174',
        'https://hospitalmanagement.intellispiders.in', // Your frontend origin
        'https://hospitalmanagementtest.intellispiders.in',
        'https://appointment-booking.intellispiders.in',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers'          => [
        // 'Authorization',
        // 'Content-Type',
        // 'X-Requested-With',
        // 'ipaddress',
        // 'timezone', // ✅ Custom header
        '*',
    ],

    'exposed_headers'          => [],

    'max_age'                  => 0,

    'supports_credentials'     => true,
];
