<?php

return [
    'paths' => ['api/*', '*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'https://your-frontend-domain.com',
        'https://cricktrade-laravel-backend.onrender.com'
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
