<?php

declare(strict_types=1);

namespace Kode;

use BadMethodCallException;

/**
 * 基础工具类
 * 提供配置管理、魔术方法、getter/setter自动处理等基础功能
 */
class Base
{
    /** 全局配置 */
    protected static array $config = [];
    /** 状态码映射 */
    protected static array $codes = [];
    /** 初始化标志 */
    protected static bool $initialized = false;

    /** 实例数据存储 */
    protected array $data = [];
    /** 扩展数据存储 */
    protected array $ext = [];

    /**
     * 构造函数
     * @param mixed ...$args 初始化参数
     */
    public function __construct(mixed ...$args)
    {
        if (!empty($args)) {
            $this->init(...$args);
        }
    }

    /**
     * 初始化方法（可被子类重写）
     * @param mixed ...$args 初始化参数
     */
    protected function init(mixed ...$args): void
    {
    }

    /**
     * 配置全局设置
     * @param array $config 配置数组
     */
    public static function configure(array $config): void
    {
        static::$config = array_merge(static::$config, $config);
    }

    /**
     * 获取配置项
     * @param string|null $key 配置键
     * @param mixed $default 默认值
     * @return mixed 配置值
     */
    public static function getConfig(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return static::$config;
        }
        return static::$config[$key] ?? $default;
    }

    /**
     * 重置配置和状态
     */
    public static function reset(): void
    {
        static::$config = [];
        static::$codes = [];
        static::$initialized = false;
    }

    /**
     * 确保已初始化
     */
    protected static function ensureInitialized(): void
    {
        if (!static::$initialized) {
            static::initialize();
            static::$initialized = true;
        }
    }

    /**
     * 初始化方法（可被子类重写）
     */
    protected static function initialize(): void
    {
    }

    /**
     * 获取值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @return mixed 值
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $this->ext[$key] ?? $default;
    }

    /**
     * 设置值（链式调用）
     * @param string $key 键名
     * @param mixed $value 值
     * @return static
     */
    public function set(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 检查键是否存在
     * @param string $key 键名
     * @return bool 是否存在
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]) || isset($this->ext[$key]);
    }

    /**
     * 删除键（链式调用）
     * @param string $key 键名
     * @return static
     */
    public function remove(string $key): static
    {
        unset($this->data[$key], $this->ext[$key]);
        return $this;
    }

    /**
     * 清空所有数据（链式调用）
     * @return static
     */
    public function clear(): static
    {
        $this->data = [];
        $this->ext = [];
        return $this;
    }

    /**
     * 获取所有数据
     * @return array 所有数据
     */
    public function all(): array
    {
        return array_merge($this->data, $this->ext);
    }

    /**
     * 转为数组
     * @return array 数组
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * 转为JSON
     * @param int $options JSON选项
     * @return string JSON字符串
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->all(), $options);
    }

    /**
     * 转为字符串
     * @return string JSON字符串
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 魔术方法 - 获取属性
     * @param string $name 属性名
     * @return mixed 属性值
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * 魔术方法 - 设置属性
     * @param string $name 属性名
     * @param mixed $value 属性值
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * 魔术方法 - 检查属性
     * @param string $name 属性名
     * @return bool 是否存在
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * 魔术方法 - 销毁属性
     * @param string $name 属性名
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    /**
     * 静态魔术方法调用
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed 返回值
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::getInstance();

        if (method_exists($instance, $method)) {
            return $instance->$method(...$args);
        }

        if (static::isGetterMethod($method)) {
            return static::handleStaticGetter($instance, $method);
        }

        if (static::isSetterMethod($method)) {
            $key = static::convertSetterToKey($method);
            return $instance->set($key, $args[0] ?? null);
        }

        if (static::isHasMethod($method)) {
            $key = static::convertHasToKey($method);
            return $instance->has($key);
        }

        if (static::isRemoveMethod($method)) {
            $key = static::convertRemoveToKey($method);
            return $instance->remove($key);
        }

        if (count($args) <= 1) {
            return $instance->set($method, $args[0] ?? null);
        }

        throw new BadMethodCallException("Static method {$method} does not exist");
    }

    /**
     * 实例魔术方法调用
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed 返回值
     */
    public function __call(string $method, array $args): mixed
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$args);
        }

        if (self::isGetterMethod($method)) {
            return $this->handleGetter($method);
        }

        if (self::isSetterMethod($method)) {
            $key = self::convertSetterToKey($method);
            return $this->set($key, $args[0] ?? null);
        }

        if (self::isHasMethod($method)) {
            $key = self::convertHasToKey($method);
            return $this->has($key);
        }

        if (self::isRemoveMethod($method)) {
            $key = self::convertRemoveToKey($method);
            return $this->remove($key);
        }

        if (count($args) <= 1) {
            return $this->set($method, $args[0] ?? null);
        }

        throw new BadMethodCallException("Method {$method} does not exist");
    }

    /**
     * 获取单例实例
     * @return static 实例
     */
    protected static function getInstance(): static
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * 检查是否为getter方法
     * @param string $name 方法名
     * @return bool 是否为getter
     */
    protected static function isGetterMethod(string $name): bool
    {
        return str_starts_with($name, 'get') && strlen($name) > 3;
    }

    /**
     * 检查是否为setter方法
     * @param string $name 方法名
     * @return bool 是否为setter
     */
    protected static function isSetterMethod(string $name): bool
    {
        return str_starts_with($name, 'set') && strlen($name) > 3;
    }

    /**
     * 检查是否为has方法
     * @param string $name 方法名
     * @return bool 是否为has
     */
    protected static function isHasMethod(string $name): bool
    {
        return str_starts_with($name, 'has') && strlen($name) > 3;
    }

    /**
     * 检查是否为remove方法
     * @param string $name 方法名
     * @return bool 是否为remove
     */
    protected static function isRemoveMethod(string $name): bool
    {
        return str_starts_with($name, 'remove') && strlen($name) > 6;
    }

    /**
     * setter方法名转换为键名
     * @param string $name 方法名
     * @return string 键名
     */
    protected static function convertSetterToKey(string $name): string
    {
        return lcfirst(substr($name, 3));
    }

    /**
     * has方法名转换为键名
     * @param string $name 方法名
     * @return string 键名
     */
    protected static function convertHasToKey(string $name): string
    {
        return lcfirst(substr($name, 3));
    }

    /**
     * remove方法名转换为键名
     * @param string $name 方法名
     * @return string 键名
     */
    protected static function convertRemoveToKey(string $name): string
    {
        return lcfirst(substr($name, 6));
    }

    /**
     * 处理getter
     * @param string $name 方法名
     * @return mixed 值
     */
    protected function handleGetter(string $name): mixed
    {
        $key = lcfirst(substr($name, 3));
        return $this->get($key);
    }

    /**
     * 处理静态getter
     * @param object $instance 实例
     * @param string $name 方法名
     * @return mixed 值
     */
    protected static function handleStaticGetter(object $instance, string $name): mixed
    {
        $key = lcfirst(substr($name, 3));
        return $instance->get($key);
    }
}
