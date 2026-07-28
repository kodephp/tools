<?php

declare(strict_types=1);

namespace Kode\Security\Storage;

use InvalidArgumentException;
use Kode\Security\Contracts\RateLimiterStorageInterface;
use Redis;

/**
 * Redis 限速存储
 *
 * 适合分布式多机部署，使用有序集合（sorted set）实现滑动窗口。
 * 需要安装并启用 ext-redis，并通过构造函数传入已连接的 Redis 实例。
 */
class RedisStorage implements RateLimiterStorageInterface
{
    private Redis $redis;
    private string $prefix;

    public function __construct(Redis $redis, string $prefix = 'kode_rl:')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function hit(string $key, int $maxAttempts, int $windowSeconds): int
    {
        $cacheKey = $this->key($key);
        $now = microtime(true);
        $windowStart = $now - $windowSeconds;

        $this->redis->zRemRangeByScore($cacheKey, '0', (string)$windowStart);
        $count = (int)$this->redis->zCard($cacheKey);
        $remaining = $maxAttempts - $count;

        if ($remaining > 0) {
            $member = $now . ':' . bin2hex(random_bytes(4));
            $this->redis->zAdd($cacheKey, $now, $member);
        }

        $this->redis->expire($cacheKey, $windowSeconds + 1);

        return $remaining;
    }

    public function remaining(string $key, int $maxAttempts, int $windowSeconds): int
    {
        if ($maxAttempts <= 0 || $windowSeconds <= 0) {
            throw new InvalidArgumentException('maxAttempts and windowSeconds must be positive');
        }

        $cacheKey = $this->key($key);
        $now = microtime(true);
        $windowStart = $now - $windowSeconds;

        $this->redis->zRemRangeByScore($cacheKey, '0', (string)$windowStart);
        $count = (int)$this->redis->zCard($cacheKey);
        $this->redis->expire($cacheKey, $windowSeconds + 1);

        return $maxAttempts - $count;
    }

    public function reset(string $key): bool
    {
        return (bool)$this->redis->del($this->key($key));
    }

    public function available(): bool
    {
        return extension_loaded('redis') && class_exists(Redis::class);
    }

    private function key(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        if ($safe === '' || strlen($safe) > 128) {
            throw new InvalidArgumentException('Invalid rate limit key');
        }
        return $this->prefix . $safe;
    }
}
