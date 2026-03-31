<?php

declare(strict_types=1);

namespace Kode\Message;

use InvalidArgumentException;

/**
 * 消息响应体 - 链式调用
 * 
 * 支持PHP8.1+特性：
 * - 只读属性
 * - 混合类型
 * - 构造函数属性提升
 * - match表达式
 * 
 * @method static Message total(int|float $value) 添加总数字段
 * @method static Message page(int $value) 添加页码字段
 * @method static Message size(int $value) 添加每页数量字段
 * @method static Message name(string $value) 添加名称字段
 */
class Message
{
    private ?int $code = null;
    private ?string $msg = null;
    private mixed $data = null;
    private array $fields = [];
    private bool $sanitize = true;
    
    private const HTTP_CODES = [
        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 204 => 'No Content',
        400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 405 => 'Method Not Allowed', 408 => 'Request Timeout',
        409 => 'Conflict', 422 => 'Unprocessable Entity', 429 => 'Too Many Requests',
        500 => 'Internal Server Error', 502 => 'Bad Gateway', 503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
    ];
    
    private const BUSINESS_CODES = [
        300000 => 'Token无效', 300001 => 'Token已过期', 300002 => '权限不足',
        400000 => '参数错误', 400001 => '缺少必填参数', 400002 => '参数格式错误',
        500000 => '数据库错误', 500001 => '数据库连接失败', 500002 => '第三方服务错误',
        600000 => '业务逻辑错误', 600001 => '资源不存在', 600002 => '资源已存在',
    ];
    
    private static array $customCodes = [];
    
    public function __construct(
        public readonly bool $sanitizeEnabled = true,
    ) {
        $this->sanitize = $sanitizeEnabled;
    }
    
    public static function code(int $code): static
    {
        return (new self())->setCode($code);
    }
    
    public static function msg(string $msg): static
    {
        return (new self())->setMsg($msg);
    }
    
    public static function data(mixed $data): static
    {
        return (new self())->setData($data);
    }
    
    public function setCode(int $code): static
    {
        $this->validateCode($code);
        $this->code = $code;
        if ($this->msg === null) {
            $this->msg = self::getMsgByCode($code);
        }
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
    
    /**
     * 魔术方法 - 支持任意链式字段
     * 
     * @param string $name 方法名（字段名）
     * @param array $arguments 参数（字段值）
     * @return static
     */
    public function __call(string $name, array $arguments): static
    {
        if ($arguments[0] !== null) {
            $this->fields[$name] = $this->sanitize 
                ? $this->sanitizeValue($arguments[0]) 
                : $arguments[0];
        }
        return $this;
    }
    
    /**
     * 静态魔术方法 - 支持类名::字段名()链式调用
     * 
     * @param string $name 方法名（字段名）
     * @param array $arguments 参数（字段值）
     * @return static
     */
    public static function __callStatic(string $name, array $arguments): static
    {
        $instance = new self();
        
        return match ($name) {
            'result' => $instance,
            'code' => isset($arguments[0]) ? $instance->setCode($arguments[0]) : $instance,
            'msg' => isset($arguments[0]) ? $instance->setMsg($arguments[0]) : $instance,
            'data' => isset($arguments[0]) ? $instance->setData($arguments[0]) : $instance,
            default => isset($arguments[0]) ? $instance->__call($name, $arguments) : $instance,
        };
    }
    
    /**
     * 输出数组结果
     */
    public function result(): array
    {
        $result = [];
        
        if ($this->code !== null) {
            $result['code'] = $this->code;
        }
        
        if ($this->msg !== null) {
            $result['msg'] = $this->msg;
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
        return $this->result();
    }
    
    public function toJson(int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        return json_encode($this->result(), $options);
    }
    
    public function __toString(): string
    {
        return $this->toJson();
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
    
    public static function success(mixed $data = null, ?string $msg = null): static
    {
        $instance = new self();
        $instance->setCode(200);
        $instance->setMsg($msg ?? '操作成功');
        if ($data !== null) {
            $instance->setData($data);
        }
        return $instance;
    }
    
    public static function error(?string $msg = null, ?int $code = null): static
    {
        return (new self())->setCode($code ?? 400)->setMsg($msg ?? '操作失败');
    }
    
    public static function notFound(?string $msg = null): static
    {
        return (new self())->setCode(404)->setMsg($msg ?? '资源不存在');
    }
    
    public static function unauthorized(?string $msg = null): static
    {
        return (new self())->setCode(401)->setMsg($msg ?? '未授权');
    }
    
    public static function forbidden(?string $msg = null): static
    {
        return (new self())->setCode(403)->setMsg($msg ?? '禁止访问');
    }
    
    public static function serverError(?string $msg = null): static
    {
        return (new self())->setCode(500)->setMsg($msg ?? '服务器错误');
    }
    
    /**
     * 添加自定义状态码映射
     * 
     * @param int $code 状态码（支持正负数）
     * @param string $msg 状态消息
     */
    public static function addCode(int $code, string $msg): void
    {
        self::$customCodes[$code] = $msg;
    }
    
    /**
     * 批量添加自定义状态码映射
     * 
     * @param array $codes 状态码映射数组 [code => msg, ...]
     */
    public static function codes(array $codes): void
    {
        self::$customCodes = [...self::$customCodes, ...$codes];
    }
    
    /**
     * 获取状态码对应的默认消息
     * 
     * @param int $code 状态码
     * @return string|null 状态消息，未找到返回null
     */
    public static function getMsgByCode(int $code): ?string
    {
        return self::$customCodes[$code] 
            ?? self::BUSINESS_CODES[$code] 
            ?? self::HTTP_CODES[$code] 
            ?? null;
    }
    
    /**
     * 清除所有自定义状态码
     */
    public static function clearCodes(): void
    {
        self::$customCodes = [];
    }
    
    /**
     * 验证状态码范围
     */
    private function validateCode(int $code): void
    {
        if ($code < -999999 || $code > 999999) {
            throw new InvalidArgumentException('Code must be between -999999 and 999999');
        }
    }
    
    /**
     * XSS防护 - 字符串消毒
     */
    private function sanitizeString(string $str): string
    {
        $str = trim($str);
        if (strlen($str) > 1000) {
            $str = substr($str, 0, 1000);
        }
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * 递归消毒数据
     */
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
                $key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key) ?: $key;
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