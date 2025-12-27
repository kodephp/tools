<?php

namespace Kode\Message;

use Kode\Message\CodeMap;

class Message
{
    private readonly CodeMap $codeMap;
    private int $code = 200;
    private string $msg = 'success';
    private mixed $data = null;
    private array $ext = [];
    private array $fieldMap = [];
    private static array $globalFieldMap = [];
    private static ?CodeMap $staticCodeMap = null;
    private static bool $codeMapInitialized = false;
    private static ?Message $staticInstance = null;
    
    public function __construct(int $code = 200, string $msg = '', mixed $data = null, ?string $customCodeFile = null)
    {
        // 确保CodeMap只初始化一次（线程安全）
        if (!self::$codeMapInitialized) {
            self::$staticCodeMap = new CodeMap($customCodeFile);
            self::$codeMapInitialized = true;
        }
        $this->codeMap = self::$staticCodeMap;
        $this->code = $code;
        $defaultMsg = $this->getCodeMsg($code);
        $this->msg = $msg ?: $defaultMsg ?: 'success';
        $this->data = $data;
    }
    
    /**
     * 设置用户自定义状态码文件
     * @param string $filePath 文件路径
     * @return $this
     */
    public function setCustomCodeFile(string $filePath): self
    {
        if (file_exists($filePath)) {
            $this->codeMap = new CodeMap($filePath);
            self::$staticCodeMap = $this->codeMap;
            self::$codeMapInitialized = true;
        }
        return $this;
    }
    
    /**
     * 重新加载用户自定义状态码文件
     * @return $this
     */
    public function reloadCustomCodeFile(): self
    {
        $this->codeMap->reloadCustomFile();
        return $this;
    }
    
    /**
     * 设置状态码
     * @param int $code 状态码（200/400/500 + 6位业务码）
     * @return $this
     */
    public function code(int $code): self
    {
        $this->code = $code;
        $defaultMsg = $this->getCodeMsg($code);
        if ($defaultMsg && empty($this->msg)) {
            $this->msg = $defaultMsg;
        }
        return $this;
    }
    
    /**
     * 设置消息文本
     * @param string $msg 消息内容
     * @return $this
     */
    public function msg(string $msg): self
    {
        $this->msg = $msg;
        return $this;
    }
    
    /**
     * 设置业务数据
     * @param mixed $data 业务数据
     * @return $this
     */
    public function data(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * 扩展自定义字段
     * @param string $key 字段名
     * @param mixed $value 字段值
     * @return $this
     */
    public function ext(string $key, mixed $value): self
    {
        $this->ext[$key] = $value;
        return $this;
    }
    
    /**
     * 批量添加自定义字段
     * @param array $fields 字段数组
     * @return $this
     */
    public function addFields(array $fields): self
    {
        $this->ext = array_merge($this->ext, $fields);
        return $this;
    }
    
    /**
     * 设置字段映射
     * @param array $map 字段映射数组
     * @return $this
     */
    public function fieldMap(array $map): self
    {
        $this->fieldMap = $map;
        return $this;
    }
    
    /**
     * 设置全局字段映射
     * @param array $map 全局字段映射数组
     */
    public static function setGlobalFieldMap(array $map): void
    {
        self::$globalFieldMap = $map;
    }
    
    /**
     * 设置字段转换配置
     * @param array $config 转换配置数组
     * @return $this
     */
    public function setFieldTransform(array $config): self
    {
        $this->fieldMap = $config;
        return $this;
    }
    
    /**
     * 设置全局字段转换配置
     * @param array $config 全局转换配置数组
     */
    public static function setGlobalFieldTransform(array $config): void
    {
        self::$globalFieldMap = $config;
    }
    
    /**
     * 输出最终结果
     * @param array $fields 额外字段映射
     * @return array 消息体数组
     */
    public function result(array $fields = []): array
    {
        // 合并全局字段映射和实例字段映射
        $map = array_merge(self::$globalFieldMap, $this->fieldMap, $fields);
        
        $result = [
            $map['code'] ?? 'code' => $this->code,
            $map['msg'] ?? 'msg' => $this->msg,
        ];
        
        if ($this->data !== null && !empty($this->data)) {
            $result[$map['data'] ?? 'data'] = $this->data;
        }
        
        if (!empty($this->ext)) {
            foreach ($this->ext as $key => $value) {
                $result[$map[$key] ?? $key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * 输出JSON格式结果
     * @param array $fields 额外字段映射
     * @return string JSON字符串
     */
    public function json(array $fields = []): string
    {
        return json_encode($this->result($fields), JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 自定义状态码映射
     * @param array $map 状态码映射数组
     * @return void
     */
    public static function setCodeMap(array $map): void
    {
        if (!self::$codeMapInitialized) {
            self::$staticCodeMap = new CodeMap();
            self::$codeMapInitialized = true;
        }
        self::$staticCodeMap->setMap($map);
    }
    
    /**
     * 获取状态码对应默认消息
     * @param int $code 状态码
     * @return string|null 默认消息
     */
    public function getCodeMsg(int $code): ?string
    {
        return $this->codeMap?->getMsg($code);
    }
    
    /**
     * 检查状态码是否存在
     * @param int $code 状态码
     * @return bool 是否存在
     */
    public function codeExists(int $code): bool
    {
        return $this->codeMap->hasCode($code);
    }
    
    /**
     * 设置自定义状态码
     * @param array $codes 状态码数组
     * @return $this
     */
    public function setCustomCodes(array $codes): self
    {
        $this->codeMap->setMap($codes);
        return $this;
    }
    
    /**
     * 添加单个状态码
     * @param int $code 状态码
     * @param string $msg 状态消息
     * @return $this
     */
    public function addCode(int $code, string $msg): self
    {
        $this->codeMap->addCode($code, $msg);
        return $this;
    }
    
    /**
     * 魔术方法 - 处理动态字段设置
     * @param string $name 方法名
     * @param array $arguments 参数数组
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        // 安全检查：禁止使用特殊方法名
        $forbiddenNames = ['__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__serialize', '__unserialize', '__toString', '__invoke', '__set_state', '__clone', '__debugInfo'];
        if (in_array($name, $forbiddenNames)) {
            throw new \BadMethodCallException("Method {$name} is not allowed");
        }
        
        // 安全检查：禁止使用包含特殊字符的方法名
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new \BadMethodCallException("Invalid method name: {$name}");
        }
        
        // 安全检查：参数数量限制
        if (count($arguments) > 1) {
            throw new \BadMethodCallException("Method {$name} accepts at most one argument");
        }
        
        $this->ext[$name] = $arguments[0] ?? '';
        return $this;
    }
    
    /**
     * 魔术方法 - 处理静态调用
     * @param string $name 方法名
     * @param array $arguments 参数数组
     * @return self|array|string
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        // 安全检查：禁止使用特殊方法名
        $forbiddenNames = ['__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__serialize', '__unserialize', '__toString', '__invoke', '__set_state', '__clone', '__debugInfo'];
        if (in_array($name, $forbiddenNames)) {
            throw new \BadMethodCallException("Method {$name} is not allowed");
        }
        
        // 安全检查：禁止使用包含特殊字符的方法名
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new \BadMethodCallException("Invalid method name: {$name}");
        }
        
        // 复用静态实例以支持链式调用
        if (self::$staticInstance === null) {
            self::$staticInstance = new self();
        }
        $instance = self::$staticInstance;
        
        // 重置消息体状态（除了codeMap）
        $instance->code = 200;
        $instance->msg = 'success';
        $instance->data = null;
        $instance->ext = [];
        $instance->fieldMap = [];
        
        switch ($name) {
            case 'result':
                $result = $instance->result($arguments[0] ?? []);
                self::$staticInstance = null;
                return $result;
            case 'json':
                $result = $instance->json($arguments[0] ?? []);
                self::$staticInstance = null;
                return $result;
            default:
                return $instance->$name(...$arguments);
        }
    }
    
    /**
     * 魔术方法 - 字符串转换
     * @return string JSON字符串
     */
    public function __toString(): string
    {
        return $this->json();
    }
}