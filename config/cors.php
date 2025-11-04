<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['RateLimit-Limit', 'RateLimit-Remaining', 'RateLimit-Reset', 'ETag'],

    'max_age' => 0,

    'supports_credentials' => true,
];
