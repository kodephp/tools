<?php

declare(strict_types=1);

namespace Kode\Curl\Exception;

class ClientException extends CurlException
{
    public function __construct(string $message = '', int $statusCode = 400, int $errorCode = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $previous);
    }
}
