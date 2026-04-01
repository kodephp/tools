<?php

declare(strict_types=1);

namespace Kode\Message;

use InvalidArgumentException;
use Throwable;

/**
 * 消息响应体 - 链式调用
 *
 * 支持灵活的链式调用方式：
 * - Message::result() - 默认200+成功
 * - Message::code(20001)->msg('错误')->result()
 * - Message::data([...])->code(20001)->result()
 * - Message::data([...])->code(20001)->msg('错误')->page(1)->name('张三')->result()
 *
 * @method static Message page(int $value) 添加页码字段
 * @method static Message size(int $value) 添加每页数量字段
 * @method static Message name(string $value) 添加名称字段
 * @method static Message total(int|float $value) 添加总数字段
 */
class Message
{
    private int $code = 200;
    private ?string $msg = null;
    private mixed $data = null;
    private array $fields = [];
    private bool $sanitize = true;

    private const DEFAULT_MSG = '成功';

    private const CODES = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        408 => 'Request Timeout',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        300000 => 'Token无效',
        300001 => 'Token已过期',
        300002 => '权限不足',
        300003 => '账户已锁定',
        400000 => '参数错误',
        400001 => '缺少必填参数',
        400002 => '参数格式错误',
        400003 => '参数超出范围',
        500000 => '数据库错误',
        500001 => '数据库连接失败',
        500002 => '第三方服务错误',
        500003 => '缓存错误',
        600000 => '业务逻辑错误',
        600001 => '资源不存在',
        600002 => '资源已存在',
        600003 => '操作失败',
    ];

    private static array $customCodes = [];
    private static ?self $instance = null;

    private const FORBIDDEN_METHODS = [
        'exec', 'system', 'shell_exec', 'passthru', 'popen', 'proc_open',
        'eval', 'assert', 'create_function',
        'unserialize', 'serialize',
        'file', 'file_get_contents', 'file_put_contents',
        'fopen', 'fwrite', 'fclose', 'readfile',
        'include', 'include_once', 'require', 'require_once',
        'mkdir', 'rmdir', 'unlink', 'rmdir',
        'curl_exec', 'curl_multi_exec',
        'mysql', 'mysqli', 'pg_', 'sqlite',
        'header', 'session_', 'cookie',
        '__construct', '__destruct', '__call', '__callStatic',
        '__get', '__set', '__isset', '__unset',
        '__invoke', '__toString', '__clone',
    ];

    public function __construct(
        public readonly bool $sanitizeEnabled = true,
    ) {
        $this->sanitize = $sanitizeEnabled;
    }

    public static function code(int $code): static
    {
        return self::getInstance()->setCode($code);
    }

    public static function msg(string $msg): static
    {
        return self::getInstance()->setMsg($msg);
    }

    public static function data(mixed $data): static
    {
        return self::getInstance()->setData($data);
    }

    public function setCode(int $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function setMsg(string $msg): static
    {
        $this->msg = $this->sanitize ? $this->sanitizeString($msg) : $msg;
        return $this;
    }

    public function setData(mixed $data): static
    {
        $this->data = $this->sanitize ? $this->sanitizeData($data) : $data;
        return $this;
    }

    public function __call(string $name, array $arguments): static
    {
        $this->validateMethodName($name);

        if (isset($arguments[0]) && $arguments[0] !== null) {
            $this->fields[$name] = $this->sanitize
                ? $this->sanitizeValue($arguments[0])
                : $arguments[0];
        }
        return $this;
    }

    public static function __callStatic(string $name, array $arguments): static
    {
        return self::getInstance()->__call($name, $arguments);
    }

    private static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function result(): array
    {
        $instance = self::getInstance();
        $result = $instance->buildResult();
        self::$instance = null;
        return $result;
    }

    private function buildResult(): array
    {
        $result = ['code' => $this->code];

        if ($this->msg !== null) {
            $result['msg'] = $this->msg;
        } else {
            $result['msg'] = self::DEFAULT_MSG;
        }

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        foreach ($this->fields as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    public function toArray(): array
    {
        return $this->buildResult();
    }

    public function toJson(int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->buildResult(), $options);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public static function codes(array $codes): void
    {
        self::$customCodes = [...self::$customCodes, ...$codes];
    }

    public static function loadCodes(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        try {
            return match ($extension) {
                'php' => self::loadCodesFromPhp($filePath),
                'json' => self::loadCodesFromJson($filePath),
                'ini' => self::loadCodesFromIni($filePath),
                default => false,
            };
        } catch (Throwable) {
            return false;
        }
    }

    private static function loadCodesFromPhp(string $filePath): bool
    {
        $codes = require $filePath;

        if (is_array($codes)) {
            self::codes($codes);
            return true;
        }

        return false;
    }

    private static function loadCodesFromJson(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        $codes = json_decode($content, true);

        if (is_array($codes)) {
            self::codes($codes);
            return true;
        }

        return false;
    }

    private static function loadCodesFromIni(string $filePath): bool
    {
        $codes = parse_ini_file($filePath, true);

        if (is_array($codes)) {
            $flatCodes = [];
            foreach ($codes as $section) {
                if (is_array($section)) {
                    foreach ($section as $key => $value) {
                        if (is_string($key)) {
                            $flatCodes[(int)$key] = $value;
                        }
                    }
                }
            }
            self::codes($flatCodes);
            return true;
        }

        return false;
    }

    public static function getMsgByCode(int $code): ?string
    {
        return self::$customCodes[$code] ?? self::CODES[$code] ?? null;
    }

    public static function getDefaultCodes(): array
    {
        return self::CODES;
    }

    public static function getAllCodes(): array
    {
        return [...self::CODES, ...self::$customCodes];
    }

    public static function clearCodes(): void
    {
        self::$customCodes = [];
    }

    public static function setCodes(array $codes): void
    {
        self::$customCodes = $codes;
    }

    private function validateMethodName(string $name): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new InvalidArgumentException("Invalid field name: {$name}");
        }

        $lowerName = strtolower($name);
        foreach (self::FORBIDDEN_METHODS as $forbidden) {
            if (str_starts_with($lowerName, $forbidden)) {
                throw new InvalidArgumentException("Forbidden field name: {$name}");
            }
        }

        if (strlen($name) > 50) {
            throw new InvalidArgumentException('Field name too long (max 50)');
        }
    }

    private function sanitizeString(string $str): string
    {
        $str = trim($str);
        if (strlen($str) > 1000) {
            $str = substr($str, 0, 1000);
        }
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function sanitizeData(mixed $data): mixed
    {
        return match (true) {
            is_string($data) => $this->sanitizeString($data),
            is_array($data) => $this->sanitizeArray($data),
            is_int($data), is_float($data), is_bool($data) => $data,
            is_null($data) => null,
            default => $data,
        };
    }

    private function sanitizeArray(array $arr): array
    {
        $result = [];
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key) ?: (string)$key;
            }
            $result[$key] = $this->sanitizeData($value);
        }
        return $result;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        return $this->sanitizeData($value);
    }
}