<?php
return [
    'default' => env('PULLER_CONNECTION', 'database'),
    'connections' => [
        'database' => [
            'driver' => 'database',
            'table' => 'puller_messages',
            'retry_after' => 90,
            'block_for' => null,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'puller',
            'queue' => env('PULLER_QUEUE', 'puller'),
            'retry_after' => 90,
            'block_for' => null,
        ],
    ],

];
