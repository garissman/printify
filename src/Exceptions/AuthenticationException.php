<?php

namespace Garissman\Printify\Exceptions;

/**
 * Exception thrown when API authentication fails (401 responses)
 */
class AuthenticationException extends PrintifyException
{
    public function __construct(string $message = 'Authentication failed. Please check your API token.')
    {
        parent::__construct($message, 401);
    }
}
