<?php

declare(strict_types=1);

namespace Kode\Curl;

use Kode\String\Str;

class Response
{
    private mixed $content;
    private int $statusCode;
    private string $contentType;
    private int $errorCode;
    private string $errorMessage;
    private array $headers = [];
    private ?string $effectiveUrl = null;

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

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function getEffectiveUrl(): ?string
    {
        return $this->effectiveUrl;
    }

    public function setEffectiveUrl(string $url): self
    {
        $this->effectiveUrl = $url;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->errorCode === 0 && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    public function isOk(): bool
    {
        return $this->statusCode === 200;
    }

    public function isCreated(): bool
    {
        return $this->statusCode === 201;
    }

    public function isNoContent(): bool
    {
        return $this->statusCode === 204;
    }

    public function isMovedPermanently(): bool
    {
        return $this->statusCode === 301;
    }

    public function isFound(): bool
    {
        return $this->statusCode === 302;
    }

    public function isNotModified(): bool
    {
        return $this->statusCode === 304;
    }

    public function isBadRequest(): bool
    {
        return $this->statusCode === 400;
    }

    public function isUnauthorized(): bool
    {
        return $this->statusCode === 401;
    }

    public function isForbidden(): bool
    {
        return $this->statusCode === 403;
    }

    public function isNotFound(): bool
    {
        return $this->statusCode === 404;
    }

    public function isMethodNotAllowed(): bool
    {
        return $this->statusCode === 405;
    }

    public function isConflict(): bool
    {
        return $this->statusCode === 409;
    }

    public function isUnprocessableEntity(): bool
    {
        return $this->statusCode === 422;
    }

    public function isTooManyRequests(): bool
    {
        return $this->statusCode === 429;
    }

    public function isInternalServerError(): bool
    {
        return $this->statusCode === 500;
    }

    public function isServiceUnavailable(): bool
    {
        return $this->statusCode === 503;
    }

    public function isJson(): bool
    {
        return Str::startsWith($this->contentType, 'application/json');
    }

    public function isXml(): bool
    {
        return Str::startsWith($this->contentType, 'application/xml')
            || Str::startsWith($this->contentType, 'text/xml');
    }

    public function isHtml(): bool
    {
        return Str::startsWith($this->contentType, 'text/html');
    }

    public function isText(): bool
    {
        return Str::startsWith($this->contentType, 'text/plain');
    }

    public function isForm(): bool
    {
        return Str::contains($this->contentType, 'application/x-www-form-urlencoded')
            || Str::contains($this->contentType, 'multipart/form-data');
    }

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

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function json(): ?array
    {
        return $this->toArray();
    }

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

    public function __toString(): string
    {
        return $this->toString();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $array = $this->toArray();
        return $array[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->toArray());
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->toArray(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->toArray(), array_flip($keys));
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->content = $this->except([$key]);
        return $value;
    }

    public static function from(string $content, string $contentType = 'application/json', int $statusCode = 200): self
    {
        if (Str::startsWith($contentType, 'application/json')) {
            $decoded = json_decode($content, true);
            $content = is_array($decoded) ? $decoded : $content;
        }

        return new self($content, $statusCode, $contentType);
    }

    public static function success(mixed $data = null, string $message = 'OK', int $statusCode = 200): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode, 'application/json');
    }

    public static function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): self
    {
        return new self([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode, 'application/json');
    }

    public static function notFound(string $message = 'Not Found'): self
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return self::error($message, 500);
    }

    public static function validationError(array $errors, string $message = 'Validation Failed'): self
    {
        return self::error($message, 422, $errors);
    }

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
