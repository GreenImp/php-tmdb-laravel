<?php

return [
    /*
     * Api key
     */
    'token' => [
        'api_key' => env('TMDB_API_KEY'),
        'bearer_token' => env('TMDB_BEARER_TOKEN'),
    ],

    /**
     * Client options
     */
    'options' => [
        /*
         * Use https
         */
        'secure' => true,
    ],

    /*
     * Cache
     */
    'cache' => [
        'enabled' => true,
        // Keep the path empty or remove it entirely to default to storage/tmdb/cache
        'path' => storage_path('tmdb/cache'),
        'lifetime' => 86400,
    ],

    /*
     * Log
     */
    'log' => [
        'enabled' => true,
        // Keep the path empty or remove it entirely to default to storage/logs/tmdb.log
        'path' => storage_path('logs/tmdb.log'),
    ]
];
