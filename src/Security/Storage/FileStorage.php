<?php

declare(strict_types=1);

namespace Kode\Security\Storage;

use InvalidArgumentException;
use Kode\Security\Contracts\RateLimiterStorageInterface;
use RuntimeException;

/**
 * 文件锁实现的限速存储
 *
 * 适合 FPM / Swoole / FrankenPHP / Workerman 等多进程环境，
 * 通过 flock 实现跨进程互斥。
 */
class FileStorage implements RateLimiterStorageInterface
{
    private string $dir;

    public function __construct(string $dir = '')
    {
        $this->setDir($dir);
    }

    public function setDir(string $dir): void
    {
        if ($dir !== '' && !is_dir($dir)) {
            if (!mkdir($dir, 0750, true) && !is_dir($dir)) {
                throw new RuntimeException("Failed to create rate limit directory: {$dir}");
            }
        }
        $this->dir = $dir;
    }

    public function getDir(): string
    {
        if ($this->dir === '') {
            $dir = sys_get_temp_dir() . '/kode_rate_limit';
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }
            return $dir;
        }
        return $this->dir;
    }

    public function hit(string $key, int $maxAttempts, int $windowSeconds): int
    {
        $file = $this->file($key);
        $handle = $this->open($file);

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new RuntimeException('Unable to acquire rate limit lock');
        }

        $record = $this->read($handle);
        $windowStart = microtime(true) - $windowSeconds;
        $record['requests'] = array_values(array_filter(
            $record['requests'] ?? [],
            static fn (float $time): bool => $time > $windowStart
        ));

        $remaining = $maxAttempts - count($record['requests']);

        if ($remaining > 0) {
            $record['requests'][] = microtime(true);
        }

        $this->write($handle, $record);

        flock($handle, LOCK_UN);
        fclose($handle);

        return $remaining;
    }

    public function remaining(string $key, int $maxAttempts, int $windowSeconds): int
    {
        if ($maxAttempts <= 0 || $windowSeconds <= 0) {
            throw new InvalidArgumentException('maxAttempts and windowSeconds must be positive');
        }

        $file = $this->file($key);
        $handle = $this->open($file);

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new RuntimeException('Unable to acquire rate limit lock');
        }

        $record = $this->read($handle);
        $windowStart = microtime(true) - $windowSeconds;
        $record['requests'] = array_values(array_filter(
            $record['requests'] ?? [],
            static fn (float $time): bool => $time > $windowStart
        ));

        $this->write($handle, $record);

        flock($handle, LOCK_UN);
        fclose($handle);

        return $maxAttempts - count($record['requests']);
    }

    public function reset(string $key): bool
    {
        $file = $this->file($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    public function available(): bool
    {
        return true;
    }

    private function file(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        if ($safe === '' || strlen($safe) > 128) {
            throw new InvalidArgumentException('Invalid rate limit key');
        }
        return $this->getDir() . '/' . $safe . '.json';
    }

    /**
     * @return resource
     */
    private function open(string $file)
    {
        $handle = fopen($file, 'c+');
        if ($handle === false) {
            throw new RuntimeException("Unable to open rate limit file: {$file}");
        }
        return $handle;
    }

    /**
     * @param resource $handle
     */
    private function read($handle): array
    {
        rewind($handle);
        $content = stream_get_contents($handle);
        if ($content === false || $content === '') {
            return ['requests' => []];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : ['requests' => []];
    }

    /**
     * @param resource $handle
     */
    private function write($handle, array $record): void
    {
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($record, JSON_UNESCAPED_UNICODE));
        fflush($handle);
    }
}
