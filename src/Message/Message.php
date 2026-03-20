<?php

declare(strict_types=1);

namespace Kode\Message;

use BadMethodCallException;
use InvalidArgumentException;

class Message
{
    protected int $code = 200;
    protected string $msg = 'success';
    protected mixed $data = null;
    protected array $headers = [];
    protected array $ext = [];
    
    protected static array $codes = [];
    protected static bool $initialized = false;
    protected static ?Message $instance = null;
    
    public function __construct()
    {
    }
    
    public static function init(): static
    {
        if (!static::$initialized) {
            static::$codes = [
                200 => 'success',
                201 => 'created',
                202 => 'accepted',
                204 => 'no content',
                400 => 'bad request',
                401 => 'unauthorized',
                403 => 'forbidden',
                404 => 'not found',
                405 => 'method not allowed',
                408 => 'request timeout',
                409 => 'conflict',
                500 => 'internal server error',
                501 => 'not implemented',
                502 => 'bad gateway',
                503 => 'service unavailable',
                504 => 'gateway timeout',
                300000 => 'token invalid',
                300001 => 'token expired',
                300002 => 'insufficient permissions',
                300003 => 'account locked',
                400000 => 'parameter error',
                400001 => 'missing required parameter',
                400002 => 'invalid parameter format',
                400003 => 'parameter out of range',
                500000 => 'database error',
                500001 => 'database connection error',
                500002 => 'third party service error',
                500003 => 'cache error',
                600000 => 'business logic error',
                600001 => 'resource not found',
                600002 => 'resource already exists',
                600003 => 'operation failed',
            ];
            static::$initialized = true;
        }
        static::$instance = new static();
        return static::$instance;
    }
    
    public static function codes(array $codes): void
    {
        static::$codes = array_merge(static::$codes, $codes);
    }
    
    public static function addCode(int $code, string $msg): void
    {
        static::$codes[$code] = $msg;
    }
    
    public static function removeCode(int $code): void
    {
        unset(static::$codes[$code]);
    }
    
    public static function clearCodes(): void
    {
        static::$codes = [];
    }
    
    public static function getCodes(): array
    {
        return static::$codes;
    }
    
    public static function getMsgByCode(int $code): ?string
    {
        return static::$codes[$code] ?? null;
    }
    
    public static function hasCode(int $code): bool
    {
        return isset(static::$codes[$code]);
    }
    
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        
        if (method_exists(static::class, $name)) {
            $result = static::$instance->$name(...$arguments);
            if ($result instanceof static) {
                static::$instance = $result;
            }
            return $result;
        }
        
        if (count($arguments) <= 1) {
            return static::$instance->addExt($name, $arguments[0] ?? null);
        }
        
        throw new BadMethodCallException("Static method {$name} does not exist");
    }
    
    public static function code(int $code): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        static::$instance->code = static::$instance->sanitizeCode($code);
        if (static::$instance->msg === 'success') {
            static::$instance->msg = static::getMsgByCode(static::$instance->code) ?? static::$instance->getDefaultMsg(static::$instance->code);
        }
        return static::$instance;
    }
    
    public static function msg(string $msg): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        static::$instance->msg = static::$instance->sanitizeString($msg, 1000);
        return static::$instance;
    }
    
    public static function data(mixed $data): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        static::$instance->data = static::$instance->sanitizeData($data);
        return static::$instance;
    }
    
    public static function page(array|int $page): static
    {
        if (is_int($page)) {
            $page = ['page' => $page];
        }
        return static::ext('page', $page);
    }
    
    public static function header(string $key, string $value): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        $safeKey = static::$instance->sanitizeKey($key);
        static::$instance->headers[$safeKey] = static::$instance->sanitizeString($value, 500);
        return static::$instance;
    }
    
    public static function headers(array $headers): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        foreach ($headers as $key => $value) {
            static::$instance->header($key, (string)$value);
        }
        return static::$instance;
    }
    
    public static function ext(string|array $key, mixed $value = null): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::$instance->addExt($k, $v);
            }
            return static::$instance;
        }
        return static::$instance->addExt($key, $value);
    }
    
    public function addExt(string $key, mixed $value): static
    {
        $safeKey = $this->sanitizeKey($key);
        if (strlen($safeKey) > 100) {
            throw new InvalidArgumentException('Extension key too long');
        }
        $this->ext[$safeKey] = $this->sanitizeData($value);
        return $this;
    }
    
    public function getExt(string $key): mixed
    {
        return $this->ext[$key] ?? null;
    }
    
    public function hasExt(string $key): bool
    {
        return isset($this->ext[$key]);
    }
    
    public static function removeExt(string $key): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        unset(static::$instance->ext[$key]);
        return static::$instance;
    }
    
    public static function clearExt(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        static::$instance->ext = [];
        return static::$instance;
    }
    
    public static function result(): array
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance->build();
    }
    
    public function getResult(): array
    {
        return $this->build();
    }
    
    public function all(): array
    {
        return $this->build();
    }
    
    public function build(): array
    {
        $result = [
            'code' => $this->code,
            'msg' => $this->msg,
        ];
        
        if ($this->data !== null) {
            $result['data'] = $this->data;
        }
        
        if (!empty($this->headers)) {
            $result['_headers'] = $this->headers;
        }
        
        foreach ($this->ext as $key => $value) {
            $result[$key] = $value;
        }
        
        return $result;
    }
    
    public function toArray(): array
    {
        return $this->build();
    }
    
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->build(), $options);
    }
    
    public function __toString(): string
    {
        return $this->toJson();
    }
    
    public function toXml(string $root = 'response'): string
    {
        return $this->arrayToXml($this->build(), $root);
    }
    
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }
        
        if (count($arguments) <= 1) {
            return $this->addExt($name, $arguments[0] ?? null);
        }
        
        throw new BadMethodCallException("Method {$name} does not exist");
    }
    
    protected function getDefaultMsg(int $code): string
    {
        return static::getMsgByCode($code) ?? match ($code) {
            200 => 'success',
            400 => '操作失败',
            401 => '未授权',
            403 => '禁止访问',
            404 => '资源不存在',
            500 => '服务器错误',
            default => '操作完成'
        };
    }
    
    protected function sanitizeCode(int $code): int
    {
        if ($code < 1 || $code > 999999) {
            throw new InvalidArgumentException('Code must be between 1 and 999999');
        }
        return $code;
    }
    
    protected function sanitizeString(string $str, int $maxLength = 1000): string
    {
        $str = trim($str);
        if (strlen($str) > $maxLength) {
            $str = substr($str, 0, $maxLength);
        }
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
    
    protected function sanitizeKey(string $key): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            throw new InvalidArgumentException("Invalid key: {$key}");
        }
        return $key;
    }
    
    protected function sanitizeData(mixed $data): mixed
    {
        if (is_string($data)) {
            return $this->sanitizeString($data);
        }
        if (is_array($data)) {
            return $this->sanitizeArray($data);
        }
        return $data;
    }
    
    protected function sanitizeArray(array $arr): array
    {
        $result = [];
        foreach ($arr as $key => $value) {
            $safeKey = $this->sanitizeKey((string)$key);
            $result[$safeKey] = $this->sanitizeData($value);
        }
        return $result;
    }
    
    protected function arrayToXml(array $data, string $root, int $depth = 0): string
    {
        if ($depth > 10) {
            return '';
        }
        
        $indent = str_repeat('  ', $depth);
        $xml = "{$indent}<{$root}>";
        
        foreach ($data as $key => $value) {
            $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$key);
            
            if (is_array($value)) {
                $xml .= "\n" . $this->arrayToXml($value, $safeKey, $depth + 1);
                $xml .= "\n{$indent}";
            } else {
                $xml .= "<{$safeKey}>" . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . "</{$safeKey}>";
            }
        }
        
        $xml .= "</{$root}>";
        return $xml;
    }
    
    public function isSuccess(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }
    
    public function isError(): bool
    {
        return $this->code >= 400;
    }
    
    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }
    
    public function isServerError(): bool
    {
        return $this->code >= 500;
    }
    
    public function isRedirect(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }
    
    public function statusCodeCategory(): string
    {
        return match (true) {
            $this->code >= 100 && $this->code < 200 => 'informational',
            $this->code >= 200 && $this->code < 300 => 'success',
            $this->code >= 300 && $this->code < 400 => 'redirect',
            $this->code >= 400 && $this->code < 500 => 'client_error',
            $this->code >= 500 && $this->code < 600 => 'server_error',
            default => 'unknown'
        };
    }
}