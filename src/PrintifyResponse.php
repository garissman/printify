<?php

namespace Garissman\Printify;

use Psr\Http\Message\ResponseInterface;

/**
 * Response wrapper for Guzzle responses
 * Provides a consistent interface similar to Laravel's Http Response
 */
class PrintifyResponse
{
    protected ResponseInterface $response;

    protected ?array $decoded = null;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the JSON decoded body of the response as an array
     */
    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->decoded === null) {
            $this->decoded = json_decode($this->response->getBody()->getContents(), true) ?? [];
        }

        if ($key === null) {
            return $this->decoded;
        }

        return data_get($this->decoded, $key, $default);
    }

    /**
     * Determine if the request was successful (2xx status code)
     */
    public function ok(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    /**
     * Determine if the response was successful (alias for ok)
     */
    public function successful(): bool
    {
        return $this->ok();
    }

    /**
     * Get the status code of the response
     */
    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get the body of the response
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get a header from the response
     */
    public function header(string $header): ?string
    {
        $values = $this->response->getHeader($header);

        return $values[0] ?? null;
    }

    /**
     * Get all headers from the response
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the underlying PSR response
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }
}
