<?php

namespace Garissman\Printify\Exceptions;

use Exception;

/**
 * Base exception for all Printify API errors
 */
class PrintifyException extends Exception
{
    protected ?array $errors;

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, ?array $errors = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
