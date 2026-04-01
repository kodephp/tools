<?php

declare(strict_types=1);

namespace Kode\Curl;

use Kode\String\Str;

/**
 * HTTP响应类
 * 封装cURL请求的响应数据
 */
class Response
{
    /** 响应内容 */
    private mixed $content;
    /** HTTP状态码 */
    private int $statusCode;
    /** 内容类型 */
    private string $contentType;
    /** 错误码 */
    private int $errorCode;
    /** 错误消息 */
    private string $errorMessage;
    /** 响应头 */
    private array $headers = [];
    /** 最终请求URL */
    private ?string $effectiveUrl = null;

    /**
     * 构造函数
     * @param mixed $content 响应内容
     * @param int $statusCode HTTP状态码
     * @param string $contentType 内容类型
     * @param int $errorCode 错误码
     * @param string $errorMessage 错误消息
     */
    public function __construct(
        mixed $content,
        int $statusCode,
        string $contentType,
        int $errorCode = 0,
        string $errorMessage = ''
    ) {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     * 获取响应内容
     * @return mixed 响应内容
     */
    public function getContent(): mixed
    {
        return $this->content;
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
     * 获取内容类型
     * @return string 内容类型
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * 获取错误码
     * @return int 错误码
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * 获取错误消息
     * @return string 错误消息
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * 获取响应头
     * @return array 响应头数组
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 设置响应头
     * @param array $headers 响应头
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 获取最终请求URL
     * @return string|null URL
     */
    public function getEffectiveUrl(): ?string
    {
        return $this->effectiveUrl;
    }

    /**
     * 设置最终请求URL
     * @param string $url URL
     * @return self
     */
    public function setEffectiveUrl(string $url): self
    {
        $this->effectiveUrl = $url;
        return $this;
    }

    /**
     * 是否成功（2xx状态码且无错误）
     * @return bool 是否成功
     */
    public function isSuccess(): bool
    {
        return $this->errorCode === 0 && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * 是否重定向（3xx状态码）
     * @return bool 是否重定向
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
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

    /**
     * 是否200 OK
     * @return bool 是否OK
     */
    public function isOk(): bool
    {
        return $this->statusCode === 200;
    }

    /**
     * 是否201 Created
     * @return bool 是否Created
     */
    public function isCreated(): bool
    {
        return $this->statusCode === 201;
    }

    /**
     * 是否204 No Content
     * @return bool 是否No Content
     */
    public function isNoContent(): bool
    {
        return $this->statusCode === 204;
    }

    /**
     * 是否301 Moved Permanently
     * @return bool 是否永久移动
     */
    public function isMovedPermanently(): bool
    {
        return $this->statusCode === 301;
    }

    /**
     * 是否302 Found
     * @return bool 是否Found
     */
    public function isFound(): bool
    {
        return $this->statusCode === 302;
    }

    /**
     * 是否304 Not Modified
     * @return bool 是否未修改
     */
    public function isNotModified(): bool
    {
        return $this->statusCode === 304;
    }

    /**
     * 是否400 Bad Request
     * @return bool 是否错误请求
     */
    public function isBadRequest(): bool
    {
        return $this->statusCode === 400;
    }

    /**
     * 是否401 Unauthorized
     * @return bool 是否未授权
     */
    public function isUnauthorized(): bool
    {
        return $this->statusCode === 401;
    }

    /**
     * 是否403 Forbidden
     * @return bool 是否禁止
     */
    public function isForbidden(): bool
    {
        return $this->statusCode === 403;
    }

    /**
     * 是否404 Not Found
     * @return bool 是否未找到
     */
    public function isNotFound(): bool
    {
        return $this->statusCode === 404;
    }

    /**
     * 是否405 Method Not Allowed
     * @return bool 是否方法不允许
     */
    public function isMethodNotAllowed(): bool
    {
        return $this->statusCode === 405;
    }

    /**
     * 是否409 Conflict
     * @return bool 是否冲突
     */
    public function isConflict(): bool
    {
        return $this->statusCode === 409;
    }

    /**
     * 是否422 Unprocessable Entity
     * @return bool 是否不可处理实体
     */
    public function isUnprocessableEntity(): bool
    {
        return $this->statusCode === 422;
    }

    /**
     * 是否429 Too Many Requests
     * @return bool 是否请求过多
     */
    public function isTooManyRequests(): bool
    {
        return $this->statusCode === 429;
    }

    /**
     * 是否500 Internal Server Error
     * @return bool 是否服务器错误
     */
    public function isInternalServerError(): bool
    {
        return $this->statusCode === 500;
    }

    /**
     * 是否503 Service Unavailable
     * @return bool 是否服务不可用
     */
    public function isServiceUnavailable(): bool
    {
        return $this->statusCode === 503;
    }

    /**
     * 是否JSON响应
     * @return bool 是否JSON
     */
    public function isJson(): bool
    {
        return Str::startsWith($this->contentType, 'application/json');
    }

    /**
     * 是否XML响应
     * @return bool 是否XML
     */
    public function isXml(): bool
    {
        return Str::startsWith($this->contentType, 'application/xml')
            || Str::startsWith($this->contentType, 'text/xml');
    }

    /**
     * 是否HTML响应
     * @return bool 是否HTML
     */
    public function isHtml(): bool
    {
        return Str::startsWith($this->contentType, 'text/html');
    }

    /**
     * 是否纯文本响应
     * @return bool 是否纯文本
     */
    public function isText(): bool
    {
        return Str::startsWith($this->contentType, 'text/plain');
    }

    /**
     * 是否表单响应
     * @return bool 是否表单
     */
    public function isForm(): bool
    {
        return Str::contains($this->contentType, 'application/x-www-form-urlencoded')
            || Str::contains($this->contentType, 'multipart/form-data');
    }

    /**
     * 转为数组
     * @return array 数组
     */
    public function toArray(): array
    {
        if ($this->content === null) {
            return [];
        }

        if (is_array($this->content)) {
            return $this->content;
        }

        if ($this->isJson()) {
            $decoded = json_decode($this->content, true);
            return is_array($decoded) ? $decoded : [];
        }

        if ($this->isForm()) {
            parse_str($this->content, $parsed);
            return $parsed;
        }

        return [];
    }

    /**
     * 转为JSON字符串
     * @return string JSON字符串
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * 获取JSON数据
     * @return array|null JSON数据
     */
    public function json(): ?array
    {
        return $this->toArray();
    }

    /**
     * 转为对象
     * @return object|null 对象
     */
    public function object(): ?object
    {
        if ($this->content === null) {
            return null;
        }

        if (is_object($this->content)) {
            return $this->content;
        }

        if ($this->isJson()) {
            return json_decode($this->content, false);
        }

        return null;
    }

    /**
     * 转为字符串
     * @return string 字符串
     */
    public function toString(): string
    {
        if (is_string($this->content)) {
            return $this->content;
        }

        if (is_array($this->content)) {
            return http_build_query($this->content);
        }

        return '';
    }

    /**
     * 转为字符串
     * @return string 字符串
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * 获取数组中的值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @return mixed 值
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $array = $this->toArray();
        return $array[$key] ?? $default;
    }

    /**
     * 检查键是否存在
     * @param string $key 键名
     * @return bool 是否存在
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->toArray());
    }

    /**
     * 只获取指定键的值
     * @param array $keys 键名数组
     * @return array 结果数组
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->toArray(), array_flip($keys));
    }

    /**
     * 排除指定键的值
     * @param array $keys 键名数组
     * @return array 结果数组
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->toArray(), array_flip($keys));
    }

    /**
     * 获取并删除指定键的值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @return mixed 值
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->content = $this->except([$key]);
        return $value;
    }

    /**
     * 从字符串创建响应
     * @param string $content 内容
     * @param string $contentType 内容类型
     * @param int $statusCode 状态码
     * @return self 响应对象
     */
    public static function from(string $content, string $contentType = 'application/json', int $statusCode = 200): self
    {
        if (Str::startsWith($contentType, 'application/json')) {
            $decoded = json_decode($content, true);
            $content = is_array($decoded) ? $decoded : $content;
        }

        return new self($content, $statusCode, $contentType);
    }

    /**
     * 创建成功响应
     * @param mixed $data 数据
     * @param string $message 消息
     * @param int $statusCode 状态码
     * @return self 响应对象
     */
    public static function success(mixed $data = null, string $message = 'OK', int $statusCode = 200): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode, 'application/json');
    }

    /**
     * 创建错误响应
     * @param string $message 错误消息
     * @param int $statusCode 状态码
     * @param mixed $errors 错误详情
     * @return self 响应对象
     */
    public static function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): self
    {
        return new self([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode, 'application/json');
    }

    /**
     * 创建404未找到响应
     * @param string $message 消息
     * @return self 响应对象
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::error($message, 404);
    }

    /**
     * 创建401未授权响应
     * @param string $message 消息
     * @return self 响应对象
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    /**
     * 创建403禁止响应
     * @param string $message 消息
     * @return self 响应对象
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    /**
     * 创建500服务器错误响应
     * @param string $message 消息
     * @return self 响应对象
     */
    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return self::error($message, 500);
    }

    /**
     * 创建422验证错误响应
     * @param array $errors 验证错误
     * @param string $message 消息
     * @return self 响应对象
     */
    public static function validationError(array $errors, string $message = 'Validation Failed'): self
    {
        return self::error($message, 422, $errors);
    }

    /**
     * 如果有错误则抛出异常
     * @return self
     * @throws \Kode\Curl\Exception\CurlException
     */
    public function throwIfError(): self
    {
        if (!$this->isSuccess()) {
            $exceptionClass = match (true) {
                $this->isClientError() => \Kode\Curl\Exception\ClientException::class,
                $this->isServerError() => \Kode\Curl\Exception\ServerException::class,
                default => \Kode\Curl\Exception\CurlException::class,
            };

            throw new $exceptionClass(
                $this->errorMessage ?: 'Request failed with status ' . $this->statusCode,
                $this->statusCode,
                $this->errorCode
            );
        }

        return $this;
    }

    /**
     * 如果不是200则抛出异常
     * @return self
     * @throws \Kode\Curl\Exception\CurlException
     */
    public function throwIfNotOk(): self
    {
        if (!$this->isOk()) {
            throw new \Kode\Curl\Exception\CurlException(
                'Expected 200 OK, got ' . $this->statusCode,
                $this->statusCode
            );
        }

        return $this;
    }
}
