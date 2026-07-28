<?php

declare(strict_types=1);

namespace Kode\Security\Contracts;

/**
 * 限速存储接口
 *
 * 实现此接口可为 Security::rateLimit() 提供不同的后端存储，
 * 以适配 FPM、Swoole、FrankenPHP、Workerman 等高并发环境。
 */
interface RateLimiterStorageInterface
{
    /**
     * 记录一次请求并返回剩余可用次数
     *
     * @param string $key 限速标识
     * @param int $maxAttempts 窗口内最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return int 剩余次数，<=0 表示已触发限速
     */
    public function hit(string $key, int $maxAttempts, int $windowSeconds): int;

    /**
     * 获取剩余可用请求次数（不增加计数）
     *
     * @param string $key 限速标识
     * @param int $maxAttempts 窗口内最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return int 剩余次数
     */
    public function remaining(string $key, int $maxAttempts, int $windowSeconds): int;

    /**
     * 重置指定 key 的限速记录
     *
     * @param string $key 限速标识
     * @return bool 是否成功
     */
    public function reset(string $key): bool;

    /**
     * 当前存储适配器是否可用
     *
     * @return bool
     */
    public function available(): bool;
}
