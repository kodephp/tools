<?php

declare(strict_types=1);

namespace Kode;

use BadMethodCallException;

abstract class Base
{
    protected static array $config = [];
    protected static array $codes = [];
    
    protected array $data = [];
    protected array $ext = [];
    
    public function __construct()
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
}