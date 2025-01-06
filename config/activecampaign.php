<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ActiveCampaign API Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may configure your ActiveCampaign API credentials. The URL and
    | API key can be found in your ActiveCampaign account settings.
    |
    */

    'url' => env('ACTIVECAMPAIGN_URL'),
    'key' => env('ACTIVECAMPAIGN_KEY'),
    'version' => env('ACTIVECAMPAIGN_VERSION', '3'),


    /*
    |--------------------------------------------------------------------------
    | Synchronization Settings
    |--------------------------------------------------------------------------
    |
    | Configure how your models sync with ActiveCampaign.
    |
    */

    'sync' => [
        // The queue connection to use for sync jobs
        'queue' => env('ACTIVECAMPAIGN_QUEUE', 'default'),

        // The queue name to use for sync jobs
        'queue_name' => env('ACTIVECAMPAIGN_QUEUE_NAME', 'activecampaign'),

        // Number of times to attempt syncing before failing
        'max_attempts' => env('ACTIVECAMPAIGN_MAX_ATTEMPTS', 3),

        // Whether to sync models by default
        'sync_by_default' => env('ACTIVECAMPAIGN_SYNC_BY_DEFAULT', true),

        // Cache duration for tags (in minutes)
        'tag_cache_duration' => env('ACTIVECAMPAIGN_TAG_CACHE_DURATION', 60),
    ],
];
