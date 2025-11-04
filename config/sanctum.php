<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:8080,127.0.0.1:8000')), 

    'guard' => ['web'],

    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'ensure_frontend_requests_are_stateful' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ],
];
