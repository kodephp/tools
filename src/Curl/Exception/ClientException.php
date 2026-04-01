<?php

declare(strict_types=1);

namespace Kode\Curl\Exception;

/**
 * 客户端异常（4xx错误）
 */
class ClientException extends CurlException
{
    /**
     * 构造函数
     * @param string $message 错误消息
     * @param int $statusCode HTTP状态码
     * @param int $errorCode CURL错误码
     * @param \Throwable|null $previous 前一个异常
     */
    public function __construct(string $message = '', int $statusCode = 400, int $errorCode = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $previous);
    }
}
