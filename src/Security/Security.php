<?php

declare(strict_types=1);

namespace Kode\Security;

use InvalidArgumentException;
use Kode\Ip\Ip;
use Kode\Security\Contracts\RateLimiterStorageInterface;
use Kode\Security\Storage\FileStorage;
use RuntimeException;

/**
 * 安全工具类
 * 提供限速、CSRF、请求签名、CIDR检查、输入过滤、请求指纹、重放防护等安全功能
 *
 * 所有方法均为静态方法，便于快速调用；限速模块支持文件锁、内存、APCu、Redis 等多种存储后端，
 * 可灵活适配 FPM / Swoole / FrankenPHP / Workerman 等高并发环境。
 */
class Security
{
    /** 默认限速窗口（秒） */
    private const DEFAULT_RATE_WINDOW = 60;
    /** 默认最大请求次数 */
    private const DEFAULT_RATE_LIMIT = 60;
    /** Token最小长度 */
    private const MIN_TOKEN_LENGTH = 16;
    /** Token最大长度 */
    private const MAX_TOKEN_LENGTH = 256;
    /** 签名算法 */
    private const SIGN_ALGO = 'sha256';
    /** 签名有效期（秒），0表示不校验时间戳 */
    private const SIGN_EXPIRE = 300;
    /** Nonce 默认 TTL（秒） */
    private const DEFAULT_NONCE_TTL = 300;

    /** @var RateLimiterStorageInterface|null 限速存储实例 */
    private static ?RateLimiterStorageInterface $rateLimiterStorage = null;

    /** @var bool 是否自动启动会话以支持 CSRF */
    private static bool $autoSession = true;

    /**
     * 设置限速存储后端
     */
    public static function setRateLimiterStorage(RateLimiterStorageInterface $storage): void
    {
        self::$rateLimiterStorage = $storage;
    }

    /**
     * 获取当前限速存储后端
     */
    public static function getRateLimiterStorage(): RateLimiterStorageInterface
    {
        if (self::$rateLimiterStorage === null) {
            self::$rateLimiterStorage = new FileStorage();
        }
        return self::$rateLimiterStorage;
    }

    /**
     * 配置限速文件存储目录（便捷方法）
     */
    public static function setRateLimitDir(string $dir): void
    {
        self::$rateLimiterStorage = new FileStorage($dir);
    }

    /**
     * 获取限速文件存储目录（便捷方法）
     */
    public static function getRateLimitDir(): string
    {
        $storage = self::getRateLimiterStorage();
        if ($storage instanceof FileStorage) {
            return $storage->getDir();
        }
        return '';
    }

    /**
     * 设置是否自动启动会话（CSRF 用）
     */
    public static function setAutoSession(bool $enabled): void
    {
        self::$autoSession = $enabled;
    }

    /**
     * 检查是否超过限速（滑动窗口）
     *
     * @param string $key 限速标识（如 IP、用户ID）
     * @param int $maxAttempts 窗口内最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return bool true = 允许通过，false = 已限速
     */
    public static function rateLimit(string $key, int $maxAttempts = self::DEFAULT_RATE_LIMIT, int $windowSeconds = self::DEFAULT_RATE_WINDOW): bool
    {
        return self::getRateLimiterStorage()->hit($key, $maxAttempts, $windowSeconds) > 0;
    }

    /**
     * 获取剩余可用请求次数（同时会记录本次请求）
     *
     * @param string $key 限速标识
     * @param int $maxAttempts 窗口内最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return int 剩余次数，<=0 表示已触发限速
     */
    public static function rateLimitRemaining(string $key, int $maxAttempts = self::DEFAULT_RATE_LIMIT, int $windowSeconds = self::DEFAULT_RATE_WINDOW): int
    {
        return self::getRateLimiterStorage()->hit($key, $maxAttempts, $windowSeconds);
    }

    /**
     * 仅查询剩余次数，不增加计数
     */
    public static function rateLimitAvailable(string $key, int $maxAttempts = self::DEFAULT_RATE_LIMIT, int $windowSeconds = self::DEFAULT_RATE_WINDOW): int
    {
        return self::getRateLimiterStorage()->remaining($key, $maxAttempts, $windowSeconds);
    }

    /**
     * 重置指定 key 的限速记录
     */
    public static function rateLimitReset(string $key): bool
    {
        return self::getRateLimiterStorage()->reset($key);
    }

    /**
     * 生成请求指纹（基于 IP + User-Agent + 可选额外参数）
     *
     * @param array $extra 额外参与哈希的字段
     * @return string 64 位十六进制指纹
     */
    public static function requestFingerprint(array $extra = []): string
    {
        $parts = [
            Ip::get() ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];

        if ($extra !== []) {
            $parts[] = json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return hash('sha256', implode('|', $parts));
    }

    /**
     * 生成一次性 Nonce（用于防重放攻击）
     *
     * @param string $namespace 命名空间
     * @param int $ttl 有效时间（秒）
     * @return string Nonce Token
     */
    public static function nonce(string $namespace = 'nonce', int $ttl = self::DEFAULT_NONCE_TTL): string
    {
        return self::randomToken(32) . ':' . $namespace;
    }

    /**
     * 验证并消耗一个 Nonce
     *
     * @param string $token 待验证的 Nonce
     * @param string $namespace 命名空间
     * @param int $ttl 有效时间（秒）
     * @return bool 是否首次有效
     */
    public static function verifyNonce(string $token, string $namespace = 'nonce', int $ttl = self::DEFAULT_NONCE_TTL): bool
    {
        if ($token === '' || !str_contains($token, ':')) {
            return false;
        }

        [$random, $ns] = explode(':', $token, 2);
        if ($random === '' || $ns !== $namespace) {
            return false;
        }

        $key = 'nonce:' . $namespace . ':' . hash('sha256', $token);
        return self::getRateLimiterStorage()->hit($key, 1, $ttl) > 0;
    }

    /**
     * 生成 CSRF Token
     *
     * @param string|null $sessionKey 存储在 $_SESSION 中的键名
     * @return string Token
     */
    public static function csrfToken(?string $sessionKey = null): string
    {
        $key = $sessionKey ?? '_kode_csrf_token';
        self::ensureSession();

        $token = self::randomToken(32);
        $_SESSION[$key] = $token;
        return $token;
    }

    /**
     * 验证 CSRF Token
     *
     * @param string $token 待验证的 Token
     * @param string|null $sessionKey 存储在 $_SESSION 中的键名
     * @param bool $clear 验证成功后是否清除 Token（一次性校验）
     * @return bool 是否有效
     */
    public static function csrfVerify(string $token, ?string $sessionKey = null, bool $clear = false): bool
    {
        if ($token === '') {
            return false;
        }

        $key = $sessionKey ?? '_kode_csrf_token';
        self::ensureSession();

        $expected = $_SESSION[$key] ?? null;
        if (!is_string($expected) || $expected === '') {
            return false;
        }

        $valid = hash_equals($expected, $token);

        if ($valid && $clear) {
            unset($_SESSION[$key]);
        }

        return $valid;
    }

    /**
     * 生成一次性 CSRF Token（验证后自动失效）
     */
    public static function csrfTokenOnce(?string $sessionKey = null): string
    {
        $key = ($sessionKey ?? '_kode_csrf_token') . '_once';
        return self::csrfToken($key);
    }

    /**
     * 验证一次性 CSRF Token
     */
    public static function csrfVerifyOnce(string $token, ?string $sessionKey = null): bool
    {
        $key = ($sessionKey ?? '_kode_csrf_token') . '_once';
        return self::csrfVerify($token, $key, true);
    }

    /**
     * 生成请求签名（HMAC-SHA256）
     *
     * @param array $data 待签名数据
     * @param string $secret 密钥
     * @param int|null $timestamp 时间戳，为 null 时使用当前时间
     * @return string 签名结果
     */
    public static function sign(array $data, string $secret, ?int $timestamp = null): string
    {
        if (strlen($secret) < 16) {
            throw new InvalidArgumentException('Secret must be at least 16 characters');
        }

        $payload = self::buildSignPayload($data, $timestamp ?? time());
        return hash_hmac(self::SIGN_ALGO, $payload, $secret);
    }

    /**
     * 验证请求签名
     *
     * @param array $data 接收到的数据（需包含 _sign、_time）
     * @param string $secret 密钥
     * @param int $expire 签名有效期（秒），0 表示不校验时间
     * @return bool 是否有效
     */
    public static function signVerify(array $data, string $secret, int $expire = self::SIGN_EXPIRE): bool
    {
        if (strlen($secret) < 16) {
            throw new InvalidArgumentException('Secret must be at least 16 characters');
        }

        $signature = $data['_sign'] ?? '';
        $timestamp = $data['_time'] ?? 0;

        if (!is_string($signature) || $signature === '' || !is_numeric($timestamp)) {
            return false;
        }

        $timestamp = (int)$timestamp;
        if ($expire > 0 && abs(time() - $timestamp) > $expire) {
            return false;
        }

        $payloadData = $data;
        unset($payloadData['_sign']);
        $payload = self::buildSignPayload($payloadData, $timestamp);

        return hash_equals(hash_hmac(self::SIGN_ALGO, $payload, $secret), $signature);
    }

    /**
     * 为请求数据附加签名字段
     *
     * @param array $data 业务数据
     * @param string $secret 密钥
     * @return array 包含 _time、_sign 的数据
     */
    public static function signPayload(array $data, string $secret): array
    {
        $timestamp = time();
        $data['_time'] = $timestamp;
        $data['_sign'] = self::sign($data, $secret, $timestamp);
        return $data;
    }

    /**
     * 检查 IP 是否属于指定 CIDR
     */
    public static function inCidr(string $ip, string $cidr): bool
    {
        return Ip::inCidr($ip, $cidr);
    }

    /**
     * 检查 IP 是否在指定范围内
     */
    public static function inRange(string $ip, string $range): bool
    {
        return Ip::inRange($ip, $range);
    }

    /**
     * 安全获取输入值（支持 GET/POST/COOKIE/REQUEST）
     *
     * @param string $key 键名
     * @param mixed $default 默认值
     * @param string $type 目标类型：string|int|float|bool|array|email|url|ip|json
     * @param string $source 数据源：get|post|cookie|request|server
     * @return mixed 过滤后的值
     */
    public static function input(string $key, mixed $default = null, string $type = 'string', string $source = 'request'): mixed
    {
        $value = match ($source) {
            'get' => $_GET[$key] ?? $default,
            'post' => $_POST[$key] ?? $default,
            'cookie' => $_COOKIE[$key] ?? $default,
            'server' => $_SERVER[$key] ?? $default,
            default => $_REQUEST[$key] ?? $default,
        };

        if ($value === $default || $value === null) {
            return $default;
        }

        return self::cast($value, $type);
    }

    /**
     * 批量安全过滤输入
     *
     * @param array $rules 规则 [['name', 'string', 'default', 'post'], ...]
     * @return array 过滤后的数据
     */
    public static function inputs(array $rules): array
    {
        $result = [];
        foreach ($rules as $rule) {
            [$key, $type] = [$rule[0], $rule[1]];
            $default = $rule[2] ?? null;
            $source = $rule[3] ?? 'request';
            $result[$key] = self::input($key, $default, $type, $source);
        }
        return $result;
    }

    /**
     * 类型转换与过滤
     *
     * @param mixed $value 原始值
     * @param string $type string|int|float|bool|array|email|url|ip|json
     * @return mixed 转换后的值
     */
    public static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) ?: '',
            'url' => filter_var($value, FILTER_VALIDATE_URL) ?: '',
            'ip' => filter_var($value, FILTER_VALIDATE_IP) ?: '',
            'json' => is_string($value) ? json_decode($value, true) : $value,
            'array' => is_array($value) ? $value : [$value],
            default => is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE),
        };
    }

    /**
     * XSS 清理
     */
    public static function xssClean(string $str): string
    {
        $str = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return preg_replace('/<(script|iframe|object|embed|form)[^>]*>.*?<\/\1>/si', '', $str) ?? $str;
    }

    /**
     * SQL 注入基础过滤（仅作为额外层，不能替代参数化查询）
     */
    public static function sqlSafe(string $str): string
    {
        return addslashes($str);
    }

    /**
     * 生成随机 Token
     *
     * @param int $length 长度
     * @return string Token
     */
    public static function randomToken(int $length = 32): string
    {
        if ($length < self::MIN_TOKEN_LENGTH || $length > self::MAX_TOKEN_LENGTH) {
            throw new InvalidArgumentException('Token length must be between ' . self::MIN_TOKEN_LENGTH . ' and ' . self::MAX_TOKEN_LENGTH);
        }
        return bin2hex(random_bytes(intdiv($length, 2)));
    }

    /**
     * 生成安全随机整数（闭区间）
     */
    public static function randomInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /**
     * 确保会话已启动
     */
    private static function ensureSession(): void
    {
        if (!self::$autoSession || PHP_SAPI === 'cli') {
            return;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * 构建签名载荷字符串
     */
    private static function buildSignPayload(array $data, int $timestamp): string
    {
        ksort($data);
        return http_build_query($data) . '&_timestamp=' . $timestamp;
    }
}
