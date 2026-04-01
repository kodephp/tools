<?php

declare(strict_types=1);

namespace Kode\Curl\Exception;

use RuntimeException;

/**
 * Curl异常基类
 */
class CurlException extends RuntimeException
{
    /** HTTP状态码 */
    private int $statusCode;
    /** CURL错误码 */
    private int $errorCode;

    /**
     * 构造函数
     * @param string $message 错误消息
     * @param int $statusCode HTTP状态码
     * @param int $errorCode CURL错误码
     * @param \Throwable|null $previous 前一个异常
     */
    public function __construct(string $message = '', int $statusCode = 0, int $errorCode = 0, ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * 获取HTTP状态码
     * @return int 状态码
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 获取CURL错误码
     * @return int 错误码
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * 是否客户端错误（4xx状态码）
     * @return bool 是否客户端错误
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * 是否服务端错误（5xx状态码）
     * @return bool 是否服务端错误
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}
