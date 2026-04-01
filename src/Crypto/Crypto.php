<?php

declare(strict_types=1);

namespace Kode\Crypto;

use Kode\Base;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;

/**
 * 加解密工具类
 * 支持AES-256-GCM加密、MD5/SHA系列哈希、密码哈希、HMAC等
 */
class Crypto extends Base
{
    /** 自动选择加密引擎 */
    public const ENGINE_AUTO = 'auto';
    /** Sodium加密引擎 */
    public const ENGINE_SODIUM = 'sodium';
    /** OpenSSL加密引擎 */
    public const ENGINE_OPENSSL = 'openssl';

    /** 标准Base64输出 */
    public const MODE_STANDARD = 'standard';
    /** URL安全Base64输出 */
    public const MODE_URL_SAFE = 'url_safe';
    /** 十六进制输出 */
    public const MODE_COMPACT = 'compact';

    /** 默认密钥（仅用于开发环境） */
    public const DEFAULT_KEY = 'kode_default_key_2025';
    /** 默认加密算法 */
    public const DEFAULT_ALGO = 'aes-256-gcm';

    /** 密钥最小长度 */
    private const MIN_KEY_LENGTH = 16;
    /** 密钥最大长度 */
    private const MAX_KEY_LENGTH = 64;
    /** 数据最大长度（字节） */
    private const MAX_DATA_LENGTH = 10_000_000;

    /** PHP版本检测标志 */
    private static bool $php85Detected = false;
    /** PHP8.5+标志 */
    private static bool $isPhp85 = false;
    /** 单例实例 */
    private static ?self $instance = null;

    /** 全局配置 */
    protected static array $config = [
        'engine' => self::ENGINE_AUTO,
        'mode' => self::MODE_STANDARD,
    ];

    /** 当前密钥 */
    private string $key;
    /** 当前引擎 */
    private string $engine;
    /** 当前模式 */
    private string $mode;

    /**
     * 构造函数
     * @param string|null $key 加密密钥
     * @param string|null $engine 加密引擎
     * @param string|null $mode 输出模式
     */
    public function __construct(?string $key = null, ?string $engine = null, ?string $mode = null)
    {
        $this->key = $this->sanitizeKey($key ?? self::DEFAULT_KEY);
        $this->engine = $this->detectEngine($engine ?? static::getConfig('engine', self::ENGINE_AUTO));
        $this->mode = $mode ?? static::getConfig('mode', self::MODE_STANDARD);
    }

    /**
     * 初始化配置
     */
    protected static function initialize(): void
    {
        if (empty(static::$config)) {
            static::$config = [
                'engine' => self::ENGINE_AUTO,
                'mode' => self::MODE_STANDARD,
            ];
        }
    }

    /**
     * 检测PHP版本是否为8.5+
     * @return bool 是否为PHP8.5+
     */
    private static function detectPhp85(): bool
    {
        if (!self::$php85Detected) {
            self::$php85Detected = true;
            self::$isPhp85 = PHP_VERSION_ID >= 80500;
        }
        return self::$isPhp85;
    }

    /**
     * 检测可用加密引擎
     * @param string $engine 引擎名称
     * @return string 可用引擎
     */
    private function detectEngine(string $engine): string
    {
        if ($engine === self::ENGINE_SODIUM) {
            // 检查Sodium是否支持AES-256-GCM
            if (extension_loaded('sodium') && defined('\\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES')) {
                return self::ENGINE_SODIUM;
            }
            // 使用ChaCha20-Poly1305作为替代
            if (extension_loaded('sodium')) {
                return self::ENGINE_SODIUM;
            }
        }
        if ($engine === self::ENGINE_OPENSSL && extension_loaded('openssl')) {
            return self::ENGINE_OPENSSL;
        }
        if ($engine === self::ENGINE_AUTO) {
            if (extension_loaded('sodium') && defined('\\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES')) {
                return self::ENGINE_SODIUM;
            }
            if (extension_loaded('sodium')) {
                return self::ENGINE_SODIUM;
            }
            if (extension_loaded('openssl')) {
                return self::ENGINE_OPENSSL;
            }
        }
        throw new RuntimeException('No encryption engine available');
    }

    /**
     * 验证并清理密钥
     * @param string $key 密钥
     * @return string 清理后的密钥
     */
    private function sanitizeKey(string $key): string
    {
        $key = trim($key);
        if (strlen($key) < self::MIN_KEY_LENGTH) {
            throw new InvalidArgumentException('Key must be at least ' . self::MIN_KEY_LENGTH . ' characters');
        }
        if (strlen($key) > self::MAX_KEY_LENGTH) {
            $key = substr($key, 0, self::MAX_KEY_LENGTH);
        }
        return $key;
    }

    /**
     * 派生密钥（SHA256）
     * @param string $key 原始密钥
     * @return string 派生后的密钥
     */
    private function deriveKey(string $key): string
    {
        return hash('sha256', $key, true);
    }

    /**
     * 验证数据长度
     * @param string $data 数据
     * @return string 验证通过的数据
     */
    private function sanitizeData(string $data): string
    {
        if (strlen($data) > self::MAX_DATA_LENGTH) {
            throw new InvalidArgumentException('Data too large for encryption');
        }
        return $data;
    }

    /**
     * 加密数据
     * @param string $data 待加密数据
     * @return string 加密后的数据
     */
    public function encrypt(string $data): string
    {
        $data = $this->sanitizeData($data);

        if ($this->engine === self::ENGINE_SODIUM) {
            return $this->encryptSodium($data);
        }
        return $this->encryptOpenSSL($data);
    }

    /**
     * 解密数据
     * @param string $encrypted 待解密数据
     * @return string 解密后的数据
     */
    public function decrypt(string $encrypted): string
    {
        if ($this->engine === self::ENGINE_SODIUM) {
            return $this->decryptSodium($encrypted);
        }
        return $this->decryptOpenSSL($encrypted);
    }

    /**
     * 设置密钥（链式调用）
     * @param string $key 密钥
     * @return static
     */
    public function key(string $key): static
    {
        $this->key = $this->sanitizeKey($key);
        return $this;
    }

    /**
     * 设置引擎（链式调用）
     * @param string $engine 引擎
     * @return static
     */
    public function engine(string $engine): static
    {
        $this->engine = $this->detectEngine($engine);
        return $this;
    }

    /**
     * 设置模式（链式调用）
     * @param string $mode 模式
     * @return static
     */
    public function mode(string $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * 使用Sodium加密（优先AES-256-GCM， fallback ChaCha20-Poly1305）
     * @param string $data 数据
     * @return string 加密数据
     */
    private function encryptSodium(string $data): string
    {
        $key = $this->deriveKey($this->key);

        // 如果支持AES-256-GCM则使用，否则使用ChaCha20-Poly1305
        if (defined('\\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES')) {
            $nonce = random_bytes(\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES);
            $encrypted = sodium_crypto_aead_aes256gcm_encrypt($data, '', $nonce, $key);
        } else {
            // 使用ChaCha20-Poly1305-IETF
            $nonce = random_bytes(\SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
            $encrypted = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($data, '', $nonce, $key);
        }

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed');
        }

        $payload = $nonce . $encrypted;

        return match ($this->mode) {
            self::MODE_URL_SAFE => rtrim(strtr(base64_encode($payload), '+/', '-_'), '='),
            self::MODE_COMPACT => bin2hex($payload),
            default => base64_encode($payload)
        };
    }

    /**
     * 使用Sodium解密
     * @param string $encrypted 加密数据
     * @return string 解密数据
     */
    private function decryptSodium(string $encrypted): string
    {
        $payload = match ($this->mode) {
            self::MODE_URL_SAFE => base64_decode(strtr($encrypted, '-_', '+/')),
            self::MODE_COMPACT => hex2bin($encrypted),
            default => base64_decode($encrypted)
        };

        if ($payload === false) {
            throw new RuntimeException('Invalid encrypted data format');
        }

        // 根据nonce长度判断使用的算法
        $aesNonceLen = defined('\\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES')
            ? \SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES
            : \SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES;

        if (strlen($payload) < $aesNonceLen) {
            throw new RuntimeException('Encrypted data too short');
        }

        $nonce = substr($payload, 0, $aesNonceLen);
        $ciphertext = substr($payload, $aesNonceLen);
        $key = $this->deriveKey($this->key);

        // 根据加密时使用的算法解密
        if (defined('\\SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES')) {
            $decrypted = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, '', $nonce, $key);
        } else {
            $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($ciphertext, '', $nonce, $key);
        }

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed - data corrupted or wrong key');
        }

        return $decrypted;
    }

    /**
     * 使用OpenSSL加密
     * @param string $data 数据
     * @return string 加密数据
     */
    private function encryptOpenSSL(string $data): string
    {
        $key = $this->deriveKey($this->key);
        $algo = self::DEFAULT_ALGO;
        $ivLength = openssl_cipher_iv_length($algo);
        $iv = random_bytes($ivLength);
        $tag = '';

        $encrypted = openssl_encrypt($data, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        $payload = $iv . $tag . $encrypted;

        return match ($this->mode) {
            self::MODE_URL_SAFE => rtrim(strtr(base64_encode($payload), '+/', '-_'), '='),
            self::MODE_COMPACT => bin2hex($payload),
            default => base64_encode($payload)
        };
    }

    /**
     * 使用OpenSSL解密
     * @param string $encrypted 加密数据
     * @return string 解密数据
     */
    private function decryptOpenSSL(string $encrypted): string
    {
        $payload = match ($this->mode) {
            self::MODE_URL_SAFE => base64_decode(strtr($encrypted, '-_', '+/')),
            self::MODE_COMPACT => hex2bin($encrypted),
            default => base64_decode($encrypted)
        };

        if ($payload === false) {
            throw new RuntimeException('Invalid encrypted data format');
        }

        $algo = self::DEFAULT_ALGO;
        $ivLength = openssl_cipher_iv_length($algo);
        $tagLength = 16;

        if (strlen($payload) < $ivLength + $tagLength) {
            throw new RuntimeException('Encrypted data too short');
        }

        $iv = substr($payload, 0, $ivLength);
        $tag = substr($payload, $ivLength, $tagLength);
        $ciphertext = substr($payload, $ivLength + $tagLength);
        $key = $this->deriveKey($this->key);

        $decrypted = openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed - data corrupted or wrong key');
        }

        return $decrypted;
    }

    /**
     * MD5哈希（支持加盐）
     * @param string $str 字符串
     * @param string $salt 盐值
     * @return string 哈希值
     */
    public static function md5(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('md5', $str . $salt);
        }
        return hash('md5', $str);
    }

    /**
     * SHA1哈希（支持加盐）
     * @param string $str 字符串
     * @param string $salt 盐值
     * @return string 哈希值
     */
    public static function sha1(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('sha1', $str . $salt);
        }
        return hash('sha1', $str);
    }

    /**
     * SHA256哈希（支持加盐）
     * @param string $str 字符串
     * @param string $salt 盐值
     * @return string 哈希值
     */
    public static function sha256(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('sha256', $str . $salt);
        }
        return hash('sha256', $str);
    }

    /**
     * 密码哈希
     * @param string $str 密码
     * @param int|null $algo 算法（默认PASSWORD_DEFAULT）
     * @return string 哈希值
     */
    public static function passwordHash(string $str, ?int $algo = null): string
    {
        return password_hash($str, $algo ?? PASSWORD_DEFAULT);
    }

    /**
     * 密码验证
     * @param string $str 密码
     * @param string $hash 哈希值
     * @return bool 是否匹配
     */
    public static function passwordVerify(string $str, string $hash): bool
    {
        return password_verify($str, $hash);
    }

    /**
     * 检查密码是否需要重新哈希
     * @param string $hash 哈希值
     * @param int|null $algo 算法（默认PASSWORD_DEFAULT）
     * @return bool 是否需要重新哈希
     */
    public static function passwordNeedsRehash(string $hash, ?int $algo = null): bool
    {
        return password_needs_rehash($hash, $algo ?? PASSWORD_DEFAULT);
    }

    /**
     * HMAC哈希
     * @param string $data 数据
     * @param string $key 密钥
     * @param string $algo 算法
     * @return string HMAC值
     */
    public static function hmac(string $data, string $key, string $algo = 'sha256'): string
    {
        if (strlen($key) < 16) {
            throw new InvalidArgumentException('HMAC key must be at least 16 characters');
        }
        return hash_hmac($algo, $data, $key);
    }

    /**
     * 通用哈希
     * @param string $data 数据
     * @param string $algo 算法
     * @return string 哈希值
     */
    public static function hash(string $data, string $algo = 'sha256'): string
    {
        return hash($algo, $data);
    }

    /**
     * 恒定时间比较（防时序攻击）
     * @param string $known 已知字符串
     * @param string $user 用户字符串
     * @return bool 是否相等
     */
    public static function hashEquals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }

    /**
     * 生成随机字符串
     * @param int $length 长度
     * @param string $charset 字符集
     * @return string 随机字符串
     */
    public static function randomString(int $length = 16, string $charset = 'alphanumeric'): string
    {
        if ($length < 4 || $length > 256) {
            throw new InvalidArgumentException('Length must be between 4 and 256');
        }

        $charsets = [
            'alphanumeric' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            'numeric' => '0123456789',
            'hex' => '0123456789abcdef',
            'special' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
            'no_ambiguous' => 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'
        ];

        $chars = $charsets[$charset] ?? $charsets['alphanumeric'];
        $result = '';
        $charsLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charsLength - 1)];
        }

        return $result;
    }

    /**
     * 生成随机Token
     * @param int $length 长度
     * @return string Token
     */
    public static function token(int $length = 32): string
    {
        if (self::detectPhp85()) {
            return bin2hex(random_bytes($length / 2));
        }
        return bin2hex(random_bytes(max(16, $length / 2)));
    }

    /**
     * 生成UUID v4
     * @return string UUID
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * 生成订单号
     * @param string $prefix 前缀
     * @return string 订单号
     */
    public static function orderId(string $prefix = ''): string
    {
        $timestamp = date('YmdHis');
        $random = random_int(1000, 9999);
        return $prefix . $timestamp . $random;
    }

    /**
     * 生成邀请码
     * @param int $length 长度
     * @return string 邀请码
     */
    public static function inviteCode(int $length = 6): string
    {
        return self::randomString($length, 'alphanumeric');
    }

    /**
     * 生成验证码
     * @param int $length 长度
     * @return string 验证码
     */
    public static function verifyCode(int $length = 6): string
    {
        return self::randomString($length, 'numeric');
    }

    /**
     * 获取单例实例
     * @param string|null $key 密钥
     * @return static 实例
     */
    public static function getInstance(?string $key = null): static
    {
        if (self::$instance === null || $key !== null) {
            self::$instance = new static($key);
        }
        return self::$instance;
    }

    /**
     * 重置单例
     */
    public static function reset(): void
    {
        self::$instance = null;
        parent::reset();
    }

    /**
     * 静态方法调用
     * @param string $name 方法名
     * @param array $arguments 参数
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $staticMethods = [
            'md5', 'sha1', 'sha256', 'passwordHash', 'passwordVerify',
            'passwordNeedsRehash', 'hmac', 'hash', 'hashEquals',
            'randomString', 'token', 'uuid', 'orderId', 'inviteCode', 'verifyCode'
        ];

        if (in_array($name, $staticMethods, true)) {
            return static::$name(...$arguments);
        }

        if ($name === 'encrypt' || $name === 'decrypt') {
            return (new static())->$name(...$arguments);
        }

        return parent::__callStatic($name, $arguments);
    }

    /**
     * 实例方法调用
     * @param string $name 方法名
     * @param array $arguments 参数
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        $methods = ['encrypt', 'decrypt', 'key', 'engine', 'mode'];

        if (in_array($name, $methods, true)) {
            return $this->$name(...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
