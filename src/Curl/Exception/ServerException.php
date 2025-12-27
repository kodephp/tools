<?php

declare(strict_types=1);

namespace Kode\Curl\Exception;

class ServerException extends CurlException
{
    public function __construct(string $message = '', int $statusCode = 500, int $errorCode = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $previous);
    }
}
