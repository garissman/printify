<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Printify API Token
    |--------------------------------------------------------------------------
    |
    | Your Printify API authentication token. You can generate this from
    | your Printify dashboard under Connections > API.
    |
    */
    'api_token' => env('PRINTIFY_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Default Shop Name
    |--------------------------------------------------------------------------
    |
    | The name of your default shop. Used by Printify::shop() to filter
    | shops when you have multiple stores connected.
    |
    */
    'default_shop_name' => env('PRINTIFY_DEFAULT_SHOP_NAME'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | A secret key used to sign webhook payloads. Required when creating
    | webhooks via PrintifyWebhooks::create().
    |
    */
    'webhook_secret' => env('PRINTIFY_WEBHOOK_SECRET'),
];
