<?php

declare(strict_types=1);

namespace Kode\Security\Storage;

use InvalidArgumentException;
use Kode\Security\Contracts\RateLimiterStorageInterface;

/**
 * APCu 限速存储
 *
 * 适合单台服务器多进程/多 worker 共享计数，性能高于文件锁。
 * 需要安装并启用 ext-apcu。
 */
class ApcuStorage implements RateLimiterStorageInterface
{
    private string $prefix;

    public function __construct(string $prefix = 'kode_rl_')
    {
        $this->prefix = $prefix;
    }

    public function hit(string $key, int $maxAttempts, int $windowSeconds): int
    {
        $cacheKey = $this->key($key);
        $windowStart = microtime(true) - $windowSeconds;
        $requests = array_values(array_filter(
            $this->fetch($cacheKey),
            static fn (float $time): bool => $time > $windowStart
        ));

        $remaining = $maxAttempts - count($requests);
        if ($remaining > 0) {
            $requests[] = microtime(true);
        }

        apcu_store($cacheKey, $requests, $windowSeconds + 1);

        return $remaining;
    }

    public function remaining(string $key, int $maxAttempts, int $windowSeconds): int
    {
        if ($maxAttempts <= 0 || $windowSeconds <= 0) {
            throw new InvalidArgumentException('maxAttempts and windowSeconds must be positive');
        }

        $cacheKey = $this->key($key);
        $windowStart = microtime(true) - $windowSeconds;
        $requests = array_values(array_filter(
            $this->fetch($cacheKey),
            static fn (float $time): bool => $time > $windowStart
        ));

        apcu_store($cacheKey, $requests, $windowSeconds + 1);

        return $maxAttempts - count($requests);
    }

    public function reset(string $key): bool
    {
        return apcu_delete($this->key($key)) === true || apcu_exists($this->key($key)) === false;
    }

    public function available(): bool
    {
        return extension_loaded('apcu') && function_exists('apcu_store');
    }

    private function key(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        if ($safe === '' || strlen($safe) > 128) {
            throw new InvalidArgumentException('Invalid rate limit key');
        }
        return $this->prefix . $safe;
    }

    /**
     * @return list<float>
     */
    private function fetch(string $cacheKey): array
    {
        $value = apcu_fetch($cacheKey, $success);
        return $success && is_array($value) ? $value : [];
    }
}
