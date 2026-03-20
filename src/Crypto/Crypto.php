<?php

declare(strict_types=1);

namespace Kode\Crypto;

use Kode\Base\Base;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;

class Crypto extends Base
{
    public const ENGINE_AUTO = 'auto';
    public const ENGINE_SODIUM = 'sodium';
    public const ENGINE_OPENSSL = 'openssl';

    public const MODE_STANDARD = 'standard';
    public const MODE_URL_SAFE = 'url_safe';
    public const MODE_COMPACT = 'compact';

    public const DEFAULT_KEY = 'kode_default_key_2025';
    public const DEFAULT_ALGO = 'aes-256-gcm';
    
    private const MIN_KEY_LENGTH = 16;
    private const MAX_KEY_LENGTH = 64;
    private const MAX_DATA_LENGTH = 10_000_000;

    private static bool $php85Detected = false;
    private static ?self $instance = null;
    
    protected static array $config = [
        'engine' => self::ENGINE_AUTO,
        'mode' => self::MODE_STANDARD,
    ];

    private string $key;
    private string $engine;
    private string $mode;

    public function __construct(?string $key = null, ?string $engine = null, ?string $mode = null)
    {
        $this->key = $this->sanitizeKey($key ?? self::DEFAULT_KEY);
        $this->engine = $this->detectEngine($engine ?? static::getConfig('engine', self::ENGINE_AUTO));
        $this->mode = $mode ?? static::getConfig('mode', self::MODE_STANDARD);
    }
    
    protected static function initialize(): void
    {
        if (empty(static::$config)) {
            static::$config = [
                'engine' => self::ENGINE_AUTO,
                'mode' => self::MODE_STANDARD,
            ];
        }
    }

    private static function detectPhp85(): bool
    {
        if (!self::$php85Detected) {
            self::$php85Detected = true;
            return PHP_VERSION_ID >= 80500;
        }
        return PHP_VERSION_ID >= 80500;
    }

    private function detectEngine(string $engine): string
    {
        if ($engine === self::ENGINE_SODIUM && extension_loaded('sodium')) {
            return self::ENGINE_SODIUM;
        }
        if ($engine === self::ENGINE_OPENSSL && extension_loaded('openssl')) {
            return self::ENGINE_OPENSSL;
        }
        if ($engine === self::ENGINE_AUTO) {
            if (extension_loaded('sodium')) {
                return self::ENGINE_SODIUM;
            }
            if (extension_loaded('openssl')) {
                return self::ENGINE_OPENSSL;
            }
        }
        throw new RuntimeException('No encryption engine available');
    }

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

    private function deriveKey(string $key): string
    {
        return hash('sha256', $key, true);
    }

    private function sanitizeData(string $data): string
    {
        if (strlen($data) > self::MAX_DATA_LENGTH) {
            throw new InvalidArgumentException('Data too large for encryption');
        }
        return $data;
    }
    
    public function encrypt(string $data): string
    {
        $data = $this->sanitizeData($data);
        
        if ($this->engine === self::ENGINE_SODIUM) {
            return $this->encryptSodium($data);
        }
        return $this->encryptOpenSSL($data);
    }

    public function decrypt(string $encrypted): string
    {
        if ($this->engine === self::ENGINE_SODIUM) {
            return $this->decryptSodium($encrypted);
        }
        return $this->decryptOpenSSL($encrypted);
    }
    
    public function key(string $key): static
    {
        $this->key = $this->sanitizeKey($key);
        return $this;
    }
    
    public function engine(string $engine): static
    {
        $this->engine = $this->detectEngine($engine);
        return $this;
    }
    
    public function mode(string $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    private function encryptSodium(string $data): string
    {
        $key = $this->deriveKey($this->key);
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES);
        $encrypted = sodium_crypto_aead_aes256gcm_encrypt($data, '', $nonce, $key);

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

        $nonceLength = SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;
        
        if (strlen($payload) < $nonceLength) {
            throw new RuntimeException('Encrypted data too short');
        }

        $nonce = substr($payload, 0, $nonceLength);
        $ciphertext = substr($payload, $nonceLength);
        $key = $this->deriveKey($this->key);

        $decrypted = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, '', $nonce, $key);

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed - data corrupted or wrong key');
        }

        return $decrypted;
    }

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
    
    public static function md5(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('md5', $str . $salt);
        }
        return hash('md5', $str);
    }
    
    public static function sha1(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('sha1', $str . $salt);
        }
        return hash('sha1', $str);
    }
    
    public static function sha256(string $str, string $salt = ''): string
    {
        if (strlen($salt) > 0) {
            return hash('sha256', $str . $salt);
        }
        return hash('sha256', $str);
    }
    
    public static function passwordHash(string $str, int $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($str, $algo);
    }
    
    public static function passwordVerify(string $str, string $hash): bool
    {
        return password_verify($str, $hash);
    }
    
    public static function passwordNeedsRehash(string $hash, int $algo = PASSWORD_DEFAULT): bool
    {
        return password_needs_rehash($hash, $algo);
    }
    
    public static function hmac(string $data, string $key, string $algo = 'sha256'): string
    {
        if (strlen($key) < 16) {
            throw new InvalidArgumentException('HMAC key must be at least 16 characters');
        }
        return hash_hmac($algo, $data, $key);
    }
    
    public static function hash(string $data, string $algo = 'sha256'): string
    {
        return hash($algo, $data);
    }
    
    public static function hashEquals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }
    
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
    
    public static function token(int $length = 32): string
    {
        if (self::detectPhp85() && PHP_VERSION_ID >= 80500) {
            return bin2hex(random_bytes($length / 2));
        }
        return bin2hex(random_bytes(max(16, $length / 2)));
    }
    
    public static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    public static function orderId(string $prefix = ''): string
    {
        $timestamp = date('YmdHis');
        $random = random_int(1000, 9999);
        return $prefix . $timestamp . $random;
    }
    
    public static function inviteCode(int $length = 6): string
    {
        return self::randomString($length, 'alphanumeric');
    }
    
    public static function verifyCode(int $length = 6): string
    {
        return self::randomString($length, 'numeric');
    }
    
    public static function getInstance(?string $key = null): static
    {
        if (self::$instance === null || $key !== null) {
            self::$instance = new static($key);
        }
        return self::$instance;
    }
    
    public static function reset(): void
    {
        self::$instance = null;
        parent::reset();
    }

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

    public function __call(string $name, array $arguments): mixed
    {
        $methods = ['encrypt', 'decrypt', 'key', 'engine', 'mode'];
        
        if (in_array($name, $methods, true)) {
            return $this->$name(...$arguments);
        }
        
        return parent::__call($name, $arguments);
    }
}