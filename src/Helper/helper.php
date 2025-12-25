<?php

use Kode\Array\Arr;
use Kode\String\Str;
use Kode\Time\Time;
use Kode\Crypto\Crypto;

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
