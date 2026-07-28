<?php

declare(strict_types=1);

namespace Kode\Message;

use InvalidArgumentException;
use Kode\Array\Arr;
use Kode\Geo\Geo;
use Kode\Ip\Ip;
use Kode\Math\Math;
use Kode\String\Str;
use Kode\Time\Time;
use Throwable;

/**
 * 消息响应体 - 链式调用
 *
 * 支持灵活的链式调用方式，实例调用与静态调用完全等价：
 * - (new Message())->result()          默认 200 + "成功"
 * - Message::result()                  同上
 * - Message::code(20001)->msg('...')->result()
 * - Message::data([...])->code(20001)->page(1)->name('张三')->result()
 * - Message::mb_strcut('张三你吃了吗', 0, 2)->result()
 *
 * 静态链式调用每次都会创建新的实例，不存在请求间状态泄漏，适合 Swoole / FrankenPHP / Workerman 等高并发长生命周期环境。
 *
 * @method static static code(int $code)
 * @method static static msg(string $msg)
 * @method static static data(mixed $data)
 * @method static static sanitize(bool $enabled = true)
 * @method static array result()
 * @method static string toJson(int $options = 320)
 * @method static array toArray()
 * @method static static page(int $value)
 * @method static static size(int $value)
 * @method static static name(string $value)
 * @method static static total(int|float $value)
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

    private const FORBIDDEN_METHODS = [
        'exec', 'system', 'shell_exec', 'passthru', 'popen', 'proc_open',
        'eval', 'assert', 'create_function',
        'unserialize', 'serialize',
        'file', 'file_get_contents', 'file_put_contents',
        'fopen', 'fwrite', 'fclose', 'readfile',
        'include', 'include_once', 'require', 'require_once',
        'mkdir', 'rmdir', 'unlink',
        'curl_exec', 'curl_multi_exec',
        'mysql', 'mysqli', 'pg_', 'sqlite',
        'header', 'session_', 'cookie',
        '__construct', '__destruct', '__call', '__callStatic',
        '__get', '__set', '__isset', '__unset',
        '__invoke', '__toString', '__clone',
    ];

    private const DELEGATED_CLASSES = [
        Str::class,
        Arr::class,
        Time::class,
        Math::class,
        Geo::class,
        Ip::class,
    ];

    public function __construct(bool $sanitizeEnabled = true)
    {
        $this->sanitize = $sanitizeEnabled;
    }

    /**
     * 静态方法入口
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (in_array($name, ['codes', 'loadCodes', 'getMsgByCode', 'getDefaultCodes', 'getAllCodes', 'clearCodes', 'setCodes'], true)) {
            return self::$name(...$arguments);
        }

        return (new self())->__call($name, $arguments);
    }

    /**
     * 动态字段与方法委托
     */
    public function __call(string $name, array $arguments): mixed
    {
        $this->validateMethodName($name);

        return match ($name) {
            'code' => $this->setCode($arguments[0] ?? 200),
            'msg' => $this->setMsg($arguments[0] ?? ''),
            'data' => $this->setData($arguments[0] ?? null),
            'sanitize' => $this->setSanitize($arguments[0] ?? true),
            'result', 'toArray' => $this->buildResult(),
            'toJson' => $this->buildJson($arguments[0] ?? (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            default => $this->handleDynamicField($name, $arguments),
        };
    }

    public function __toString(): string
    {
        return $this->buildJson();
    }

    private function setCode(int $code): static
    {
        $this->code = $code;
        return $this;
    }

    private function setMsg(string $msg): static
    {
        $this->msg = $this->sanitize ? $this->sanitizeString($msg) : $msg;
        return $this;
    }

    private function setData(mixed $data): static
    {
        $this->data = $this->sanitize ? $this->sanitizeData($data) : $data;
        return $this;
    }

    private function setSanitize(bool $enabled): static
    {
        $this->sanitize = $enabled;
        return $this;
    }

    private function buildResult(): array
    {
        $result = ['code' => $this->code];

        if ($this->msg !== null) {
            $result['msg'] = $this->msg;
        } elseif ($this->code === 200) {
            $result['msg'] = self::DEFAULT_MSG;
        } else {
            $result['msg'] = self::getMsgByCode($this->code) ?? self::DEFAULT_MSG;
        }

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        foreach ($this->fields as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    private function buildJson(int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->buildResult(), $options);
    }

    private function handleDynamicField(string $name, array $arguments): static
    {
        if (count($arguments) === 1) {
            $value = $arguments[0];
            $this->fields[$name] = $this->sanitize ? $this->sanitizeValue($value) : $value;
            return $this;
        }

        foreach (self::DELEGATED_CLASSES as $class) {
            if (method_exists($class, $name)) {
                $value = $class::$name(...$arguments);
                $this->fields[$name] = $this->sanitize ? $this->sanitizeValue($value) : $value;
                return $this;
            }
        }

        throw new InvalidArgumentException("Unsupported field method: {$name}");
    }

    /**
     * 合并自定义状态码
     */
    public static function codes(array $codes): void
    {
        self::$customCodes = array_replace(self::$customCodes, $codes);
    }

    /**
     * 从文件加载状态码映射
     */
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

    /**
     * 根据状态码获取默认消息
     */
    public static function getMsgByCode(int $code): ?string
    {
        return self::$customCodes[$code] ?? self::CODES[$code] ?? null;
    }

    /**
     * 获取内置状态码映射
     */
    public static function getDefaultCodes(): array
    {
        return self::CODES;
    }

    /**
     * 获取所有状态码映射（含自定义）
     */
    public static function getAllCodes(): array
    {
        return self::$customCodes + self::CODES;
    }

    /**
     * 清空自定义状态码
     */
    public static function clearCodes(): void
    {
        self::$customCodes = [];
    }

    /**
     * 覆盖自定义状态码
     */
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
