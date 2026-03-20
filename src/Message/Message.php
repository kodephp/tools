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
    protected array $fieldMap = [];
    
    protected static array $codes = [];
    
    public function __construct(int $code = 200, ?string $msg = null, mixed $data = null)
    {
        $this->code = $code;
        $this->msg = $msg ?? $this->getDefaultMsg($code);
        $this->data = $data;
    }
    
    public static function configure(array $config): void
    {
        if (isset($config['codes'])) {
            static::$codes = array_merge(static::$codes, $config['codes']);
        }
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
        static::ensureInitialized();
        return static::$codes;
    }
    
    public static function getMsgByCode(int $code): ?string
    {
        static::ensureInitialized();
        return static::$codes[$code] ?? null;
    }
    
    public static function hasCode(int $code): bool
    {
        static::ensureInitialized();
        return isset(static::$codes[$code]);
    }
    
    protected static function ensureInitialized(): void
    {
        static $initialized = false;
        if (!$initialized) {
            static::initialize();
            $initialized = true;
        }
    }
    
    protected static function initialize(): void
    {
        if (empty(static::$codes)) {
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
        }
    }
    
    public function code(int $code): static
    {
        $this->code = $this->sanitizeCode($code);
        if ($this->msg === 'success') {
            $defaultMsg = static::getMsgByCode($this->code);
            if ($defaultMsg) {
                $this->msg = $defaultMsg;
            }
        }
        return $this;
    }
    
    public function msg(string $msg): static
    {
        $this->msg = $this->sanitizeString($msg, 1000);
        return $this;
    }
    
    public function data(mixed $data): static
    {
        $this->data = $this->sanitizeData($data);
        return $this;
    }
    
    public function page(array $page): static
    {
        return $this->ext('page', $this->sanitizeArray($page));
    }
    
    public function header(string $key, string $value): static
    {
        $safeKey = $this->sanitizeKey($key);
        $this->headers[$safeKey] = $this->sanitizeString($value, 500);
        return $this;
    }
    
    public function headers(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($key, (string)$value);
        }
        return $this;
    }
    
    public function ext(string|array $key, mixed $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->ext($k, $v);
            }
            return $this;
        }
        
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
    
    public function removeExt(string $key): static
    {
        unset($this->ext[$key]);
        return $this;
    }
    
    public function clearExt(): static
    {
        $this->ext = [];
        return $this;
    }
    
    public function fieldMap(array $map): static
    {
        $this->fieldMap = $this->sanitizeArray($map);
        return $this;
    }
    
    public function all(): array
    {
        return $this->result();
    }
    
    public function result(array $fields = []): array
    {
        $map = array_merge($this->fieldMap, $this->sanitizeArray($fields));
        
        $codeKey = $map['code'] ?? 'code';
        $msgKey = $map['msg'] ?? 'msg';
        $dataKey = $map['data'] ?? 'data';
        
        $result = [
            $codeKey => $this->code,
            $msgKey => $this->msg,
        ];
        
        if ($this->data !== null) {
            $result[$dataKey] = $this->data;
        }
        
        if (!empty($this->headers)) {
            $result['_headers'] = $this->headers;
        }
        
        foreach ($this->ext as $key => $value) {
            $resultKey = $map[$key] ?? $key;
            $result[$resultKey] = $value;
        }
        
        return $result;
    }
    
    public function toArray(): array
    {
        return $this->result();
    }
    
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->result(), $options);
    }
    
    public function __toString(): string
    {
        return $this->toJson();
    }
    
    public function toXml(string $root = 'response'): string
    {
        return $this->arrayToXml($this->result(), $root);
    }
    
    public static function success(?string $msg = null, mixed $data = null): static
    {
        return new static(200, $msg, $data);
    }
    
    public static function error(?string $msg = null, ?int $code = null, mixed $data = null): static
    {
        return new static($code ?? 400, $msg, $data);
    }
    
    public static function warning(?string $msg = null, mixed $data = null): static
    {
        return new static(400, $msg ?? '操作警告', $data);
    }
    
    public static function info(?string $msg = null, mixed $data = null): static
    {
        return new static(200, $msg ?? '提示信息', $data);
    }
    
    public static function notFound(?string $msg = null): static
    {
        return new static(404, $msg ?? '资源不存在');
    }
    
    public static function unauthorized(?string $msg = null): static
    {
        return new static(401, $msg ?? '未授权');
    }
    
    public static function forbidden(?string $msg = null): static
    {
        return new static(403, $msg ?? '禁止访问');
    }
    
    public static function serverError(?string $msg = null, mixed $data = null): static
    {
        return new static(500, $msg ?? '服务器错误', $data);
    }
    
    public static function ok(mixed $data = null, ?string $msg = null): static
    {
        return new static(200, $msg ?? '操作成功', $data);
    }
    
    public static function fail(?string $msg = null, ?int $code = null): static
    {
        return new static($code ?? 400, $msg ?? '操作失败');
    }
    
    public static function created(mixed $data = null, ?string $msg = null): static
    {
        return new static(201, $msg ?? '创建成功', $data);
    }
    
    public static function noContent(): static
    {
        return new static(204, 'no content');
    }
    
    public function reset(): static
    {
        $this->code = 200;
        $this->msg = 'success';
        $this->data = null;
        $this->headers = [];
        $this->ext = [];
        $this->fieldMap = [];
        return $this;
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->ext[$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): static
    {
        $this->ext[$key] = $value;
        return $this;
    }
    
    public function has(string $key): bool
    {
        return isset($this->ext[$key]);
    }
    
    public function remove(string $key): static
    {
        unset($this->ext[$key]);
        return $this;
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
        if ($code < 100 || $code > 999) {
            throw new InvalidArgumentException('Code must be between 100 and 999');
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
    
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (method_exists(static::class, $name)) {
            return static::$name(...$arguments);
        }
        throw new BadMethodCallException("Static method {$name} does not exist");
    }
    
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }
        if (count($arguments) <= 1) {
            return $this->set($name, $arguments[0] ?? null);
        }
        throw new BadMethodCallException("Method {$name} does not exist");
    }
}