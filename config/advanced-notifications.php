<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | The default channels to use when sending notifications if none are specified.
    | Supported: "database", "fcm", "mail", "sms"
    |
    */
    'default_channels' => ['database', 'fcm'],

    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for Firebase Cloud Messaging.
    |
    */
    'firebase' => [
        'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase_credentials.json')),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Notification Storage
    |--------------------------------------------------------------------------
    |
    | Configuration for storing notifications in the database.
    |
    */
    'database' => [
        'table_name' => 'advanced_notifications',
        'expire_after_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Define rate limits to prevent spamming users.
    |
    */
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 5,
        'decay_minutes' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Queue connection and name for processing notifications.
    |
    */
    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'database'),
        'queue' => 'notifications',
    ],
];
