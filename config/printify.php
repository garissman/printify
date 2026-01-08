<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Printify API Token
    |--------------------------------------------------------------------------
    |
    | Your Printify API token for authentication. You can generate one from
    | your Printify account settings under Connections > API.
    |
    */
    'api_token' => env('PRINTIFY_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Printify API. Default is v1, but you can override
    | this if you need to use a different API version.
    |
    */
    'base_url' => env('PRINTIFY_BASE_URL', 'https://api.printify.com/v1/'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response from the API before
    | timing out. Default is 30 seconds.
    |
    */
    'timeout' => env('PRINTIFY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic retry behavior for failed requests. Retries are
    | performed with exponential backoff for rate limits and server errors.
    |
    */
    'retry_attempts' => env('PRINTIFY_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('PRINTIFY_RETRY_DELAY', 1000), // milliseconds

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable debug logging for API requests. Useful for development but
    | should be disabled in production.
    |
    */
    'logging' => env('PRINTIFY_LOGGING', false),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign and verify webhook payloads. This is
    | required when creating webhooks through the API.
    |
    */
    'webhook_secret' => env('PRINTIFY_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Default Shop Name
    |--------------------------------------------------------------------------
    |
    | When multiple shops exist, this name is used to automatically select
    | the default shop. If not set, the first shop will be used.
    |
    */
    'default_shop_name' => env('PRINTIFY_DEFAULT_SHOP_NAME'),
];
