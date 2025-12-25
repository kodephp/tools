<?php

use Kode\Array\Arr;
use Kode\String\Str;
use Kode\Time\Time;
use Kode\Crypto\Crypto;
use Kode\Geo\Geo;
use Kode\Ip\Ip;

if (!function_exists('arr_get')) {
    /**
     * 获取数组中的值
     * @param array $array 数组
     * @param string|int $key 键名
     * @param mixed $default 默认值
     * @return mixed 值
     */
    function arr_get(array $array, string|int $key, mixed $default = null): mixed
    {
        return Arr::get($array, $key, $default);
    }
}

if (!function_exists('arr_set')) {
    /**
     * 设置数组中的值
     * @param array $array 数组
     * @param string|int $key 键名
     * @param mixed $value 值
     * @return array 修改后的数组
     */
    function arr_set(array $array, string|int $key, mixed $value): array
    {
        return Arr::set($array, $key, $value);
    }
}

if (!function_exists('arr_has')) {
    /**
     * 检查数组中是否存在键名
     * @param array $array 数组
     * @param string|int $key 键名
     * @return bool 是否存在
     */
    function arr_has(array $array, string|int $key): bool
    {
        return Arr::has($array, $key);
    }
}

if (!function_exists('str_random')) {
    /**
     * 生成随机字符串
     * @param int $length 长度
     * @param string $chars 字符集
     * @return string 随机字符串
     */
    function str_random(int $length = 16, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'): string
    {
        return Str::random($length, $chars);
    }
}

if (!function_exists('str_uuid')) {
    /**
     * 生成UUID
     * @return string UUID
     */
    function str_uuid(): string
    {
        return Str::uuid();
    }
}

if (!function_exists('str_camel')) {
    /**
     * 驼峰命名
     * @param string $str 字符串
     * @return string 驼峰命名
     */
    function str_camel(string $str): string
    {
        return Str::camel($str);
    }
}

if (!function_exists('time_now')) {
    /**
     * 获取当前时间
     * @param string $format 格式
     * @return string 当前时间
     */
    function time_now(string $format = 'Y-m-d H:i:s'): string
    {
        return Time::now($format);
    }
}

if (!function_exists('time_format')) {
    /**
     * 格式化时间
     * @param int|null $timestamp 时间戳
     * @param string $format 格式
     * @return string 格式化后的时间
     */
    function time_format(?int $timestamp = null, string $format = 'Y-m-d H:i:s'): string
    {
        return Time::format($timestamp, $format);
    }
}

if (!function_exists('crypto_md5')) {
    /**
     * MD5加密
     * @param string $str 字符串
     * @param string $salt 盐
     * @return string 加密后的字符串
     */
    function crypto_md5(string $str, string $salt = ''): string
    {
        return Crypto::md5($str, $salt);
    }
}

if (!function_exists('crypto_encrypt')) {
    /**
     * 加密
     * @param string $str 字符串
     * @param string $key 密钥
     * @return string 加密后的字符串
     */
    function crypto_encrypt(string $str, string $key): string
    {
        return Crypto::encrypt($str, $key);
    }
}

if (!function_exists('crypto_decrypt')) {
    /**
     * 解密
     * @param string $str 字符串
     * @param string $key 密钥
     * @return string 解密后的字符串
     */
    function crypto_decrypt(string $str, string $key): string
    {
        return Crypto::decrypt($str, $key);
    }
}

if (!function_exists('str_mask_phone')) {
    /**
     * 手机号脱敏
     * @param string $phone 手机号
     * @param int $start 开始位置
     * @param int $end 结束位置
     * @return string 脱敏后的手机号
     */
    function str_mask_phone(string $phone, int $start = 3, int $end = 4): string
    {
        return Str::maskPhone($phone, $start, $end);
    }
}

if (!function_exists('str_mask_id_card')) {
    /**
     * 身份证号脱敏
     * @param string $idCard 身份证号
     * @param int $start 开始位置
     * @param int $end 结束位置
     * @return string 脱敏后的身份证号
     */
    function str_mask_id_card(string $idCard, int $start = 6, int $end = 4): string
    {
        return Str::maskIdCard($idCard, $start, $end);
    }
}

if (!function_exists('arr_deep_merge')) {
    /**
     * 数组深度合并
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 合并后的数组
     */
    function arr_deep_merge(array $array1, array $array2): array
    {
        return Arr::deepMerge($array1, $array2);
    }
}

if (!function_exists('arr_multi_sort')) {
    /**
     * 多维数组排序
     * @param array $array 数组
     * @param array $keys 排序键
     * @param array $orders 排序顺序
     * @return array 排序后的数组
     */
    function arr_multi_sort(array $array, array $keys, array $orders = []): array
    {
        return Arr::multiSort($array, $keys, $orders);
    }
}

if (!function_exists('geo_distance')) {
    /**
     * Calculate distance between two coordinates
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @param string $unit Unit of measurement (km, mi, m)
     * @return float Distance between points
     */
    function geo_distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'km'): float
    {
        return Geo::distance($lat1, $lon1, $lat2, $lon2, $unit);
    }
}

if (!function_exists('ip_get_real')) {
    /**
     * Get real client IP address
     * @return string|null Real IP address or null if not found
     */
    function ip_get_real(): ?string
    {
        return Ip::getRealIp();
    }
}

if (!function_exists('ip_is_valid')) {
    /**
     * Validate IP address format
     * @param string $ip IP address to validate
     * @return bool True if valid IP address
     */
    function ip_is_valid(string $ip): bool
    {
        return Ip::isValid($ip);
    }
}

if (!function_exists('ip_is_private')) {
    /**
     * Check if IP address is private/internal
     * @param string $ip IP address to check
     * @return bool True if private IP
     */
    function ip_is_private(string $ip): bool
    {
        return Ip::isPrivate($ip);
    }
}
