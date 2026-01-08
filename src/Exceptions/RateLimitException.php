<?php

namespace Garissman\Printify\Exceptions;

/**
 * Exception thrown when API rate limit is exceeded (429 responses)
 */
class RateLimitException extends PrintifyException
{
    protected ?int $retryAfter;

    public function __construct(string $message = 'Rate limit exceeded. Please wait before making more requests.', ?int $retryAfter = null)
    {
        parent::__construct($message, 429);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
