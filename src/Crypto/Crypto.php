<?php

namespace Kode\Crypto;

class Crypto
{
    // 加密引擎常量
    public const ENGINE_AUTO = 'auto';
    public const ENGINE_SODIUM = 'sodium';
    public const ENGINE_OPENSSL = 'openssl';

    // 加密模式常量
    public const MODE_STANDARD = 'standard';
    public const MODE_URL_SAFE = 'url_safe';
    public const MODE_COMPACT = 'compact';
    // 默认密钥
    public const SECURE_CRYPTO_KEY = 'default_secure_key_2025';

    // 默认加密算法
    public const DEFAULT_ALGORITHM = 'AES-256-GCM';

    private readonly string $key;
    private readonly string $engine;
    private readonly string $mode;

    // 线程安全的缓存
    private static array $cache = [];
    private static bool $cacheInitialized = false;

    /**
     * 构造函数
     *
     * @param string $key 加密密钥
     * @param string $engine 加密引擎
     * @param string $mode 加密模式
     */
    public function __construct(string $key = self::SECURE_CRYPTO_KEY, string $engine = self::ENGINE_AUTO, string $mode = self::MODE_STANDARD)
    {
        $this->key = $key;
        $this->engine = $this->selectEngine($engine);
        $this->mode = $mode;
        
        // 初始化缓存（线程安全）
        if (!self::$cacheInitialized) {
            self::$cache = [];
            self::$cacheInitialized = true;
        }
    }

    /**
     * 获取默认密钥
     * @return string
     */
    public static function getDefaultKey(): string
    {
        return self::SECURE_CRYPTO_KEY;
    }

    /**
     * 选择加密引擎
     *
     * @param string|null $engine
     * @return string
     */
    private function selectEngine(?string $engine = null): string
    {
        // 如果没有指定引擎，则使用实例的引擎设置
        if ($engine === null) {
            $engine = $this->engine;
        }

        if ($engine === self::ENGINE_SODIUM && extension_loaded('sodium')) {
            return self::ENGINE_SODIUM;
        } elseif ($engine === self::ENGINE_OPENSSL && extension_loaded('openssl')) {
            return self::ENGINE_OPENSSL;
        } elseif ($engine === self::ENGINE_AUTO) {
            if (extension_loaded('sodium')) {
                return self::ENGINE_SODIUM;
            } elseif (extension_loaded('openssl')) {
                return self::ENGINE_OPENSSL;
            }
        }

        throw new \RuntimeException('No available encryption engine found');
    }
    
    /**
     * 加密数据
     *
     * @param string $data 要加密的数据
     * @return string 加密后的字符串
     */
    public function encrypt(string $data): string
    {
        if ($this->engine === self::ENGINE_SODIUM) {
            return $this->encryptWithSodium($data);
        } else {
            return $this->encryptWithOpenSSL($data);
        }
    }

    /**
     * 解密数据
     *
     * @param string $encrypted 加密后的字符串
     * @return string 解密后的数据
     * @throws \Exception
     */
    public function decrypt(string $encrypted): string
    {
        $engine = $this->selectEngine($this->engine);

        if ($engine === self::ENGINE_SODIUM) {
            return $this->decryptWithSodium($encrypted);
        } else {
            return $this->decryptWithOpenSSL($encrypted);
        }
    }
    
    /**
     * 使用Sodium加密
     * @param string $data
     * @return string
     */
    private function encryptWithSodium(string $data): string
    {
        $key = hash('sha256', $this->key, true); // 确保密钥长度为32字节
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES);
        $encrypted = sodium_crypto_aead_aes256gcm_encrypt($data, '', $nonce, $key);
        // Use sodium's built-in base64 encoding for better compatibility
if ($this->mode === self::MODE_URL_SAFE) {
    $result = sodium_bin2base64($nonce . $encrypted, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING);
} else {
    $result = base64_encode($nonce . $encrypted);
}

        // 根据模式进行处理
        if ($this->mode === self::MODE_COMPACT) {
            // 转换为十六进制字符串，去除非十六进制字符
            if ($this->mode === self::MODE_URL_SAFE) {
                $result = bin2hex(sodium_base642bin($result, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING));
            } else {
                $result = bin2hex(base64_decode($result));
            }
        }

        return $result;
    }

    /**
     * 使用Sodium解密
     * @param string $encrypted
     * @return string
     * @throws \Exception
     */
    private function decryptWithSodium(string $encrypted): string
    {
        // 根据模式进行预处理
        if ($this->mode === self::MODE_URL_SAFE) {
            $encrypted = str_pad(strtr($encrypted, '-_', '+/'), strlen($encrypted) % 4, '=', STR_PAD_RIGHT);
        } elseif ($this->mode === self::MODE_COMPACT) {
            // 对于紧凑型，先将十六进制转回二进制
            $encrypted = hex2bin($encrypted);
        }

        // 对于URL_SAFE和STANDARD模式，需要base64解码
        if ($this->mode !== self::MODE_COMPACT) {
            $data = base64_decode($encrypted);
        } else {
            $data = $encrypted;
        }

        $key = hash('sha256', $this->key, true); // 确保密钥长度为32字节
        $nonceLength = SODIUM_CRYPTO_AEAD_AES256GCM_NPUBBYTES;

        if (\strlen($data) < $nonceLength) {
            throw new \Exception('Invalid encrypted data');
        }

        $nonce = substr($data, 0, $nonceLength);
        $ciphertext = substr($data, $nonceLength);

        $decrypted = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, '', $nonce, $key);

        if ($decrypted === false) {
            throw new \Exception('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * 使用OpenSSL加密
     * @param string $data
     * @return string
     * @throws \Exception
     */
    private function encryptWithOpenSSL(string $data): string
    {
        $key = hash('sha256', $this->key, true); // 确保密钥长度正确
        $ivLength = openssl_cipher_iv_length(self::DEFAULT_ALGORITHM);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $tag = '';

        $encrypted = openssl_encrypt($data, self::DEFAULT_ALGORITHM, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($encrypted === false) {
            throw new \Exception('Encryption failed: ' . openssl_error_string());
        }

        $result = base64_encode($iv . $tag . $encrypted);

        // 根据模式进行处理
        if ($this->mode === self::MODE_URL_SAFE) {
            $result = strtr($result, '+/', '-_');
            $result = rtrim($result, '=');
        } elseif ($this->mode === self::MODE_COMPACT) {
            // 转换为十六进制字符串，去除非十六进制字符
            $result = bin2hex(base64_decode($result));
        }

        return $result;
    }

    /**
     * 使用OpenSSL解密
     * @param string $encrypted
     * @return string
     * @throws \Exception
     */
    private function decryptWithOpenSSL(string $encrypted): string
    {
        // 根据模式进行预处理
        if ($this->mode === self::MODE_URL_SAFE) {
            $encrypted = str_pad(strtr($encrypted, '-_', '+/'), strlen($encrypted) % 4, '=', STR_PAD_RIGHT);
        } elseif ($this->mode === self::MODE_COMPACT) {
            // 对于紧凑型，先将十六进制转回二进制
            $data = hex2bin($encrypted);
        }

        // 对于URL_SAFE和STANDARD模式，需要base64解码
        if ($this->mode !== self::MODE_COMPACT) {
            $key = hash('sha256', $this->key, true); // 确保密钥长度正确
            $data = base64_decode($encrypted);
        }

        $ivLength = openssl_cipher_iv_length(self::DEFAULT_ALGORITHM);
        $tagLength = 16; // GCM标签长度

        if (\strlen($data) < $ivLength + $tagLength) {
            throw new \Exception('Invalid encrypted data');
        }

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, $tagLength);
        $ciphertext = substr($data, $ivLength + $tagLength);

        $key = hash('sha256', $this->key, true); // 确保密钥长度正确
        $decrypted = openssl_decrypt($ciphertext, self::DEFAULT_ALGORITHM, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($decrypted === false) {
            throw new \Exception('Decryption failed: ' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * MD5加密（支持加盐）
     * @param string $str 待加密字符串
     * @param string $salt 盐值
     * @return string 加密结果
     */
    public function md5(string $str, string $salt = ''): string
    {
        return \md5($str . $salt);
    }
    

    
    /**
     * 密码哈希加密
     * @param string $str 待加密密码
     * @return string 哈希结果
     */
    public static function passwordHash(string $str): string
    {
        return \password_hash($str, PASSWORD_DEFAULT);
    }

    /**
     * 密码验证
     * @param string $str 待验证密码
     * @param string $hash 哈希值
     * @return bool 验证结果
     */
    public static function passwordVerify(string $str, string $hash): bool
    {
        return \password_verify($str, $hash);
    }

    /**
     * HMAC签名
     * @param string $str 字符串
     * @param string $key 密钥
     * @param string $algo 算法
     * @return string 签名结果
     */
    public static function hmac(string $str, string $key, string $algo = 'sha256'): string
    {
        return \hash_hmac($algo, $str, $key);
    }

    /**
     * SSL对称加密
     * @param string $str 待加密字符串
     * @param string $key 加密密钥
     * @return string 加密结果
     */
    public function sslEncrypt(string $str, string $key): string
    {
        $method = self::DEFAULT_ALGORITHM;
        $ivLength = \openssl_cipher_iv_length($method);
        $iv = \openssl_random_pseudo_bytes($ivLength);
        $tag = '';
        $encrypted = \openssl_encrypt(
            $str,
            $method,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        return \base64_encode($iv . $tag . $encrypted);
    }
    
    /**
     * SSL对称解密
     * @param string $str 待解密字符串
     * @param string $key 解密密钥
     * @return string 解密结果
     */
    public function sslDecrypt(string $str, string $key): string
    {
        $str = \base64_decode($str);
        $method = self::DEFAULT_ALGORITHM;
        $ivLength = \openssl_cipher_iv_length($method);
        $tagLength = 16; // GCM标签长度
        $iv = \substr($str, 0, $ivLength);
        $tag = \substr($str, $ivLength, $tagLength);
        $encrypted = \substr($str, $ivLength + $tagLength);
        
        return \openssl_decrypt(
            $encrypted,
            $method,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }

    /**
     * 魔术方法 - 处理动态方法调用
     * @param string $name 方法名
     * @param array $arguments 参数数组
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }
        throw new \BadMethodCallException("Method {$name} does not exist");
    }

    /**
     * 魔术方法 - 处理静态调用
     * @param string $name 方法名
     * @param array $arguments 参数数组
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new self();
        if (method_exists($instance, $name)) {
            return $instance->$name(...$arguments);
        }
        throw new \BadMethodCallException("Method {$name} does not exist");
    }

    /**
     * 生成订单号
     * @param string $prefix 前缀
     * @return string 订单号
     */
    public function order(string $prefix = ''): string
    {
        $date = \date('YmdHis');
        $random = \random_int(1000, 9999);
        return $prefix . $date . $random;
    }

    /**
     * 生成邀请码
     * @param int $length 邀请码长度
     * @param string $chars 可选字符集
     * @return string 邀请码
     */
    public function invite(int $length = 6, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        $inviteCode = '';
        $charsLength = \strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $inviteCode .= $chars[\random_int(0, $charsLength - 1)];
        }
        return $inviteCode;
    }

    /**
     * 生成URL安全码
     * @param int $length 安全码长度
     * @return string URL安全码
     */
    public function url(int $length = 16): string
    {
        $bytes = \random_bytes($length);
        return \rtrim(\strtr(\base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * 生成注册码
     * @param int $length 注册码长度
     * @param int $segmentLength 分段长度
     * @param string $separator 分隔符
     * @return string 注册码
     */
    public function reg(int $length = 16, int $segmentLength = 4, string $separator = '-'): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $registrationCode = '';
        $charsLength = \strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $registrationCode .= $chars[\random_int(0, $charsLength - 1)];
            if (($i + 1) % $segmentLength === 0 && $i !== $length - 1) {
                $registrationCode .= $separator;
            }
        }
        return $registrationCode;
    }
    

}