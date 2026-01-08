<?php

namespace Garissman\Printify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Utility class for verifying Printify webhook signatures
 *
 * Printify signs webhook payloads using HMAC-SHA256 with your webhook secret.
 * Use this class to verify that incoming webhook requests are authentic.
 */
class WebhookSignature
{
    /**
     * Verify that a webhook request is authentic
     *
     * @param Request $request - The incoming HTTP request
     * @param string|null $secret - Optional secret override (defaults to config)
     * @return bool
     */
    public static function verify(Request $request, ?string $secret = null): bool
    {
        $secret = $secret ?? Config::get('printify.webhook_secret');

        if (!$secret) {
            return false;
        }

        $signature = $request->header('X-Printify-Signature');

        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate a signature for a payload (useful for testing)
     *
     * @param string $payload - The raw payload string
     * @param string|null $secret - Optional secret override (defaults to config)
     * @return string
     */
    public static function sign(string $payload, ?string $secret = null): string
    {
        $secret = $secret ?? Config::get('printify.webhook_secret');

        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Create a middleware-friendly verification callback
     *
     * Usage in routes:
     * Route::post('/webhooks/printify', [WebhookController::class, 'handle'])
     *     ->middleware(WebhookSignature::middleware());
     *
     * @return \Closure
     */
    public static function middleware(): \Closure
    {
        return function ($request, $next) {
            if (!static::verify($request)) {
                abort(401, 'Invalid webhook signature');
            }

            return $next($request);
        };
    }
}
