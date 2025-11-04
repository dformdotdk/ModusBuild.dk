<?php

return [
    'default' => env('PENNANT_DRIVER', 'array'),

    'stores' => [
        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'features',
            'scope' => 'tenant_id',
        ],
    ],
];
