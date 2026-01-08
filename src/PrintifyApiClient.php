<?php

namespace Garissman\Printify;

use Garissman\Printify\Exceptions\AuthenticationException;
use Garissman\Printify\Exceptions\PrintifyException;
use Garissman\Printify\Exceptions\RateLimitException;
use Garissman\Printify\Exceptions\ValidationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrintifyApiClient
{
    private ?PendingRequest $client = null;
    private int $rateLimitRemaining = -1;
    private ?int $rateLimitReset = null;

    public function __construct(?string $token = null)
    {
        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'Accept' => 'application/json',
        ];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $baseUrl = Config::get('printify.base_url', 'https://api.printify.com/v1/');
        $timeout = Config::get('printify.timeout', 30);

        $options = [
            'base_uri' => $baseUrl,
            'verify' => false,
            'timeout' => $timeout,
        ];

        $this->client = Http::withOptions($options)
            ->withHeaders($headers);
    }

    public static function exchangeCodeForToken(string $app_id, string $code): Response
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens?app_id=' . $app_id . '&code=' . $code, 'POST');
    }

    /**
     * Does a HTTP request with retry logic and proper exception handling
     *
     * @param string $uri - The URI to hit
     * @param string $method - The HTTP method
     * @param array $payload
     * @return Response
     * @throws ConnectionException
     * @throws PrintifyException
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ValidationException
     */
    public function doRequest(string $uri, string $method = 'GET', array $payload = []): Response
    {
        if ($method === 'GET') {
            if (!isset($payload['paginate'])) {
                $payload['paginate'] = true;
                if (!isset($payload['page'])) {
                    $payload['page'] = 1;
                }
            }
        }

        $maxRetries = Config::get('printify.retry_attempts', 3);
        $retryDelay = Config::get('printify.retry_delay', 1000);
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                // Log request if enabled
                if (Config::get('printify.logging', false)) {
                    Log::debug('Printify API Request', [
                        'uri' => $uri,
                        'method' => $method,
                        'attempt' => $attempt,
                    ]);
                }

                $response = $this->client->$method($uri, $payload);

                // Track rate limit headers
                $this->updateRateLimitInfo($response);

                // Check for errors
                if ($response->failed()) {
                    $this->handleErrorResponse($response);
                }

                return $response;

            } catch (RequestException $e) {
                $lastException = $e;
                $statusCode = $e->response?->status();

                // Don't retry client errors (except rate limiting)
                if ($statusCode >= 400 && $statusCode < 500 && $statusCode !== 429) {
                    $this->handleErrorResponse($e->response);
                }

                // Retry on rate limit or server errors
                if ($statusCode === 429 || $statusCode >= 500) {
                    if ($attempt < $maxRetries) {
                        $sleepMs = $this->calculateBackoff($attempt, $retryDelay, $e->response);
                        usleep($sleepMs * 1000);
                        continue;
                    }
                }

                throw $e;

            } catch (ConnectionException $e) {
                $lastException = $e;

                // Retry on connection errors
                if ($attempt < $maxRetries) {
                    $sleepMs = $this->calculateBackoff($attempt, $retryDelay);
                    usleep($sleepMs * 1000);
                    continue;
                }

                throw $e;
            }
        }

        // If we get here, we've exhausted retries
        if ($lastException) {
            throw $lastException;
        }

        throw new PrintifyException('Request failed after ' . $maxRetries . ' attempts');
    }

    /**
     * Update rate limit tracking from response headers
     */
    private function updateRateLimitInfo(Response $response): void
    {
        $remaining = $response->header('X-RateLimit-Remaining');
        $reset = $response->header('X-RateLimit-Reset');

        if ($remaining !== null) {
            $this->rateLimitRemaining = (int)$remaining;
        }
        if ($reset !== null) {
            $this->rateLimitReset = (int)$reset;
        }
    }

    /**
     * Handle error responses and throw appropriate exceptions
     *
     * @throws AuthenticationException
     * @throws RateLimitException
     * @throws ValidationException
     * @throws PrintifyException
     */
    private function handleErrorResponse(Response $response): void
    {
        $statusCode = $response->status();
        $body = $response->json();
        $message = $body['message'] ?? $response->body();
        $errors = $body['errors'] ?? null;

        match ($statusCode) {
            401 => throw new AuthenticationException(
                $message ?: 'Invalid or expired API token'
            ),
            429 => throw new RateLimitException(
                $message ?: 'Rate limit exceeded',
                $response->header('Retry-After') ? (int)$response->header('Retry-After') : null
            ),
            422 => throw new ValidationException(
                $message ?: 'Validation failed',
                $errors
            ),
            default => throw new PrintifyException(
                $message ?: 'API request failed',
                $statusCode,
                null,
                $errors
            ),
        };
    }

    /**
     * Calculate backoff delay with exponential increase
     */
    private function calculateBackoff(int $attempt, int $baseDelay, ?Response $response = null): int
    {
        // Check for Retry-After header
        if ($response) {
            $retryAfter = $response->header('Retry-After');
            if ($retryAfter && is_numeric($retryAfter)) {
                return (int)$retryAfter * 1000;
            }
        }

        // Exponential backoff: baseDelay * 2^(attempt-1)
        return $baseDelay * pow(2, $attempt - 1);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public static function updateAccessToken(string $app_id, string $refresh_token): Response
    {
        $client = new self();
        return $client->doRequest('app/oauth/tokens/refresh?app_id=' . $app_id . '&refresh_token=' . $refresh_token, 'POST');
    }

    /**
     * Get current rate limit status
     *
     * @return array{remaining: int, reset: int|null}
     */
    public function getRateLimitStatus(): array
    {
        return [
            'remaining' => $this->rateLimitRemaining,
            'reset' => $this->rateLimitReset,
        ];
    }
}
