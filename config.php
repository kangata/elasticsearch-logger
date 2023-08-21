<?php

return [
    'elastic' => [
        'host' => env('ELASTIC_HOST'),
        'api_key' => env('ELASTIC_API_KEY'),
        'index' => env('ELASTIC_LOGS_INDEX'),
    ],
];
