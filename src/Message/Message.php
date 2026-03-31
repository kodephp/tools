<?php

declare(strict_types=1);

namespace Kode\Message;

use InvalidArgumentException;
use Throwable;

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
    
    /**
     * 默认状态码映射表
     */
    private const CODES = [
        // 2xx 成功
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        
        // 3xx 重定向
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        
        // 4xx 客户端错误
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        408 => 'Request Timeout',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        
        // 5xx 服务端错误
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        
        // 业务错误码
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
    
    /**
     * 开发者自定义状态码（可覆盖默认）
     */
    private static array $customCodes = [];
    private static ?self $instance = null;
    
    /**
     * 禁止的方法名列表（危险函数等）
     */
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
    
    /**
     * 设置状态码，自动获取对应消息
     */
    public static function code(int $code): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->setCode($code);
    }
    
    /**
     * 设置消息内容
     */
    public static function msg(string $msg): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->setMsg($msg);
    }
    
    /**
     * 设置数据
     */
    public static function data(mixed $data): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->setData($data);
    }
    
    /**
     * 设置状态码
     */
    public function setCode(int $code): static
    {
        $this->code = $code;
        if ($this->msg === null) {
            $this->msg = self::getMsgByCode($code);
        }
        return $this;
    }
    
    /**
     * 设置消息
     */
    public function setMsg(string $msg): static
    {
        $this->msg = $this->sanitize ? $this->sanitizeString($msg) : $msg;
        return $this;
    }
    
    /**
     * 设置数据
     */
    public function setData(mixed $data): static
    {
        $this->data = $this->sanitize ? $this->sanitizeData($data) : $data;
        return $this;
    }
    
    /**
     * 魔术方法 - 支持任意链式字段
     */
    public function __call(string $name, array $arguments): static
    {
        $this->validateMethodName($name);
        
        if ($arguments[0] !== null) {
            $this->fields[$name] = $this->sanitize 
                ? $this->sanitizeValue($arguments[0]) 
                : $arguments[0];
        }
        return $this;
    }
    
    /**
     * 静态魔术方法 - 支持类名::字段名()链式调用
     */
    public static function __callStatic(string $name, array $arguments): static
    {
        // result() 返回当前实例，不创建新的
        if ($name === 'result') {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        // 首次调用或result()后，创建新实例
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return match ($name) {
            'code' => isset($arguments[0]) ? self::$instance->setCode($arguments[0]) : self::$instance,
            'msg' => isset($arguments[0]) ? self::$instance->setMsg($arguments[0]) : self::$instance,
            'data' => isset($arguments[0]) ? self::$instance->setData($arguments[0]) : self::$instance,
            default => isset($arguments[0]) ? self::$instance->__call($name, $arguments) : self::$instance,
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
        
        // 重置静态实例，允许下次链式调用
        self::$instance = null;
        
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
    
    /**
     * 设置/合并自定义状态码映射
     * 
     * @param array $codes 状态码映射 [code => msg, ...]
     */
    public static function codes(array $codes): void
    {
        self::$customCodes = [...self::$customCodes, ...$codes];
    }
    
    /**
     * 从文件加载状态码配置
     * 
     * @param string $filePath 配置文件路径，支持 .php, .json, .ini
     * @return bool 加载是否成功
     * 
     * 示例：
     *   Message::loadCodes('config/codes.php');
     *   Message::loadCodes('config/codes.json');
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
    
    /**
     * 从PHP文件加载
     */
    private static function loadCodesFromPhp(string $filePath): bool
    {
        $codes = require $filePath;
        
        if (is_array($codes)) {
            self::codes($codes);
            return true;
        }
        
        return false;
    }
    
    /**
     * 从JSON文件加载
     */
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
    
    /**
     * 从INI文件加载
     */
    private static function loadCodesFromIni(string $filePath): bool
    {
        $codes = parse_ini_file($filePath, true);
        
        if (is_array($codes)) {
            // INI文件可能嵌套，扁平化处理
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
     * 获取状态码对应的消息
     * 优先级：自定义码 > 默认码
     */
    public static function getMsgByCode(int $code): ?string
    {
        return self::$customCodes[$code] ?? self::CODES[$code] ?? null;
    }
    
    /**
     * 获取所有默认状态码映射
     */
    public static function getDefaultCodes(): array
    {
        return self::CODES;
    }
    
    /**
     * 获取所有当前状态码映射（默认+自定义）
     */
    public static function getAllCodes(): array
    {
        return [...self::CODES, ...self::$customCodes];
    }
    
    /**
     * 清除所有自定义状态码
     */
    public static function clearCodes(): void
    {
        self::$customCodes = [];
    }
    
    /**
     * 批量设置状态码映射（完全替换默认+自定义）
     */
    public static function setCodes(array $codes): void
    {
        self::$customCodes = $codes;
    }
    
    /**
     * 验证方法名是否安全
     */
    private function validateMethodName(string $name): void
    {
        // 只允许字母、数字、下划线
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new InvalidArgumentException("Invalid field name: {$name}");
        }
        
        // 检查危险方法名
        $lowerName = strtolower($name);
        foreach (self::FORBIDDEN_METHODS as $forbidden) {
            if (str_starts_with($lowerName, $forbidden)) {
                throw new InvalidArgumentException("Forbidden field name: {$name}");
            }
        }
        
        // 限制长度
        if (strlen($name) > 50) {
            throw new InvalidArgumentException('Field name too long (max 50)');
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