<?php

declare(strict_types=1);

namespace BinanceApi\Query\Exception;

class ResponseException extends \RuntimeException
{
    private array $errors;

    public function __construct(array $errors, $code = 0, \Throwable $previous = null)
    {
        parent::__construct('The request returned errors.', $code, $previous);

        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
