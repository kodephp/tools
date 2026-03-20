<?php

declare(strict_types=1);

namespace Kode;

use BadMethodCallException;

class Base
{
    protected static array $config = [];
    protected static array $codes = [];
    protected static bool $initialized = false;
    
    protected array $data = [];
    protected array $ext = [];
    
    public function __construct(mixed ...$args)
    {
        if (!empty($args)) {
            $this->init(...$args);
        }
    }
    
    protected function init(mixed ...$args): void
    {
    }
    
    public static function configure(array $config): void
    {
        static::$config = array_merge(static::$config, $config);
    }
    
    public static function getConfig(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return static::$config;
        }
        return static::$config[$key] ?? $default;
    }
    
    public static function reset(): void
    {
        static::$config = [];
        static::$codes = [];
        static::$initialized = false;
    }
    
    protected static function ensureInitialized(): void
    {
        if (!static::$initialized) {
            static::initialize();
            static::$initialized = true;
        }
    }
    
    protected static function initialize(): void
    {
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $this->ext[$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function has(string $key): bool
    {
        return isset($this->data[$key]) || isset($this->ext[$key]);
    }
    
    public function remove(string $key): static
    {
        unset($this->data[$key], $this->ext[$key]);
        return $this;
    }
    
    public function clear(): static
    {
        $this->data = [];
        $this->ext = [];
        return $this;
    }
    
    public function all(): array
    {
        return array_merge($this->data, $this->ext);
    }
    
    public function toArray(): array
    {
        return $this->all();
    }
    
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->all(), $options);
    }
    
    public function __toString(): string
    {
        return $this->toJson();
    }
    
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }
    
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }
    
    public function __unset(string $name): void
    {
        $this->remove($name);
    }
    
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
    
    protected static function getInstance(): static
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }
    
    protected static function isGetterMethod(string $name): bool
    {
        return str_starts_with($name, 'get') && strlen($name) > 3;
    }
    
    protected static function isSetterMethod(string $name): bool
    {
        return str_starts_with($name, 'set') && strlen($name) > 3;
    }
    
    protected static function isHasMethod(string $name): bool
    {
        return str_starts_with($name, 'has') && strlen($name) > 3;
    }
    
    protected static function isRemoveMethod(string $name): bool
    {
        return str_starts_with($name, 'remove') && strlen($name) > 6;
    }
    
    protected static function convertSetterToKey(string $name): string
    {
        return lcfirst(substr($name, 3));
    }
    
    protected static function convertHasToKey(string $name): string
    {
        return lcfirst(substr($name, 3));
    }
    
    protected static function convertRemoveToKey(string $name): string
    {
        return lcfirst(substr($name, 6));
    }
    
    protected function handleGetter(string $name): mixed
    {
        $key = lcfirst(substr($name, 3));
        return $this->get($key);
    }
    
    protected static function handleStaticGetter(object $instance, string $name): mixed
    {
        $key = lcfirst(substr($name, 3));
        return $instance->get($key);
    }
}