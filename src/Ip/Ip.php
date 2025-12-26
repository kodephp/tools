<?php

namespace Kode\Ip;

/**
 * IP地址工具类
 * 提供IP地址获取、验证和操作功能
 */
class Ip
{
    /**
     * 获取真实客户端IP地址
     * @return string|null 真实IP地址，如果未找到则返回null
     */
    public static function getRealIp(): ?string
    {
        $ip = null;
        
        // 先检查常见的代理头
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ipList = explode(',', $_SERVER[$header]);
                foreach ($ipList as $ipCandidate) {
                    $ipCandidate = trim($ipCandidate);
                    if (self::isValid($ipCandidate) && !self::isPrivate($ipCandidate)) {
                        return $ipCandidate;
                    }
                }
            }
        }
        
        // 回退到REMOTE_ADDR
        if (isset($_SERVER['REMOTE_ADDR']) && self::isValid($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        return null;
    }

    /**
     * 验证IP地址格式
     * @param string $ip 要验证的IP地址
     * @return bool 如果IP地址有效则返回true
     */
    public static function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * 检查IP地址是否为私有/内部地址
     * @param string $ip 要检查的IP地址
     * @return bool 如果是私有IP则返回true
     */
    public static function isPrivate(string $ip): bool
    {
        if (!self::isValid($ip)) {
            return false;
        }
        
        $long = ip2long($ip);
        if ($long === false) {
            return false;
        }
        
        // 检查私有IP范围
        $privateRanges = [
            ['start' => ip2long('10.0.0.0'), 'end' => ip2long('10.255.255.255')],
            ['start' => ip2long('172.16.0.0'), 'end' => ip2long('172.31.255.255')],
            ['start' => ip2long('192.168.0.0'), 'end' => ip2long('192.168.255.255')],
            ['start' => ip2long('127.0.0.0'), 'end' => ip2long('127.255.255.255')],
            ['start' => ip2long('169.254.0.0'), 'end' => ip2long('169.254.255.255')]
        ];
        
        foreach ($privateRanges as $range) {
            if ($long >= $range['start'] && $long <= $range['end']) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 获取IP地址版本
     * @param string $ip IP地址
     * @return int|null 4, 6, 如果无效则返回null
     */
    public static function getVersion(string $ip): ?int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }
        
        return null;
    }

    /**
     * 将IP地址转换为长整数
     * @param string $ip IP地址
     * @return int|false 长整数，如果无效则返回false
     */
    public static function toLong(string $ip): int|false
    {
        return ip2long($ip);
    }

    /**
     * 将长整数转换为IP地址
     * @param int $long 长整数
     * @return string|false IP地址，如果无效则返回false
     */
    public static function fromLong(int $long): string|false
    {
        return long2ip($long);
    }

    /**
     * 获取IP地址位置信息（需要外部API）
     * @param string $ip IP地址
     * @param string $apiKey 可选的位置服务API密钥
     * @return array|null 位置信息，如果不可用则返回null
     */
    public static function getLocation(string $ip, ?string $apiKey = null): ?array
    {
        if (!self::isValid($ip) || self::isPrivate($ip)) {
            return null;
        }
        
        // 使用ip-api.com示例（非商业用途免费）
        $url = "http://ip-api.com/json/{$ip}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || $data['status'] !== 'success') {
            return null;
        }
        
        return [
            'ip' => $ip,
            'country' => $data['country'] ?? null,
            'countryCode' => $data['countryCode'] ?? null,
            'region' => $data['regionName'] ?? null,
            'regionCode' => $data['region'] ?? null,
            'city' => $data['city'] ?? null,
            'zip' => $data['zip'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lon' => $data['lon'] ?? null,
            'timezone' => $data['timezone'] ?? null,
            'isp' => $data['isp'] ?? null,
            'org' => $data['org'] ?? null,
            'as' => $data['as'] ?? null
        ];
    }

    /**
     * 检查IP地址是否来自特定国家
     * @param string $ip IP地址
     * @param string $countryCode 两位国家代码
     * @return bool 如果IP来自指定国家则返回true
     */
    public static function isFromCountry(string $ip, string $countryCode): bool
    {
        $location = self::getLocation($ip);
        if (!$location) {
            return false;
        }
        
        return strtoupper($location['countryCode'] ?? '') === strtoupper($countryCode);
    }

    /**
     * 获取IP地址类型（公共/私有/回环）
     * @param string $ip IP地址
     * @return string|null IP地址类型
     */
    public static function getType(string $ip): ?string
    {
        if (!self::isValid($ip)) {
            return null;
        }
        
        if (self::isPrivate($ip)) {
            return 'private';
        }
        
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'loopback';
        }
        
        return 'public';
    }
}