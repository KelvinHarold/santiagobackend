<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Only allow requests from your actual frontend origin.
    // Add your production domain here when deploying (e.g. 'https://yourdomain.com').
    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000',
        'http://server.pincodes.co.tz',
        'https://server.pincodes.co.tz',
        'http://pincodes.co.tz',
        'https://pincodes.co.tz',
        'http://www.pincodes.co.tz',
        'https://www.pincodes.co.tz',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => false,

];
