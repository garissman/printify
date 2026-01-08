<?php

namespace Garissman\Printify\Exceptions;

/**
 * Exception thrown when API validation fails (400/422 responses)
 */
class ValidationException extends PrintifyException
{
    public function __construct(string $message = 'Validation failed.', ?array $errors = null)
    {
        parent::__construct($message, 422, null, $errors);
    }
}
