<?php

declare(strict_types=1);

namespace Kode\Security\Storage;

use InvalidArgumentException;
use Kode\Security\Contracts\RateLimiterStorageInterface;

/**
 * 内存限速存储
 *
 * 数据仅保存在当前进程内存中，适合单元测试或单进程长生命周期服务。
 * 多进程 FPM 环境下请使用 FileStorage / APCuStorage / RedisStorage。
 */
class MemoryStorage implements RateLimiterStorageInterface
{
    /** @var array<string, array{requests: list<float>}> */
    private static array $buckets = [];

    public function hit(string $key, int $maxAttempts, int $windowSeconds): int
    {
        $windowStart = microtime(true) - $windowSeconds;
        self::$buckets[$key]['requests'] = array_values(array_filter(
            self::$buckets[$key]['requests'] ?? [],
            static fn (float $time): bool => $time > $windowStart
        ));

        $remaining = $maxAttempts - count(self::$buckets[$key]['requests']);
        if ($remaining > 0) {
            self::$buckets[$key]['requests'][] = microtime(true);
        }

        return $remaining;
    }

    public function remaining(string $key, int $maxAttempts, int $windowSeconds): int
    {
        if ($maxAttempts <= 0 || $windowSeconds <= 0) {
            throw new InvalidArgumentException('maxAttempts and windowSeconds must be positive');
        }

        $windowStart = microtime(true) - $windowSeconds;
        self::$buckets[$key]['requests'] = array_values(array_filter(
            self::$buckets[$key]['requests'] ?? [],
            static fn (float $time): bool => $time > $windowStart
        ));

        return $maxAttempts - count(self::$buckets[$key]['requests']);
    }

    public function reset(string $key): bool
    {
        unset(self::$buckets[$key]);
        return true;
    }

    public function available(): bool
    {
        return true;
    }

    /**
     * 清空所有内存限速记录（主要用于测试）
     */
    public static function flush(): void
    {
        self::$buckets = [];
    }
}
