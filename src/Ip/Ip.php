<?php

namespace Kode\Ip;

/**
 * IP address utility class
 * Provides IP address retrieval, validation, and manipulation
 */
class Ip
{
    /**
     * Get real client IP address
     * @return string|null Real IP address or null if not found
     */
    public static function getRealIp(): ?string
    {
        $ip = null;
        
        // Check common proxy headers first
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
        
        // Fallback to REMOTE_ADDR
        if (isset($_SERVER['REMOTE_ADDR']) && self::isValid($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        return null;
    }

    /**
     * Validate IP address format
     * @param string $ip IP address to validate
     * @return bool True if valid IP address
     */
    public static function isValid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Check if IP address is private/internal
     * @param string $ip IP address to check
     * @return bool True if private IP
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
        
        // Check private IP ranges
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
     * Get IP address version
     * @param string $ip IP address
     * @return int|null 4, 6, or null if invalid
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
     * Convert IP address to long integer
     * @param string $ip IP address
     * @return int|false Long integer or false if invalid
     */
    public static function toLong(string $ip): int|false
    {
        return ip2long($ip);
    }

    /**
     * Convert long integer to IP address
     * @param int $long Long integer
     * @return string|false IP address or false if invalid
     */
    public static function fromLong(int $long): string|false
    {
        return long2ip($long);
    }

    /**
     * Get IP address location information (requires external API)
     * @param string $ip IP address
     * @param string $apiKey Optional API key for location services
     * @return array|null Location information or null if not available
     */
    public static function getLocation(string $ip, ?string $apiKey = null): ?array
    {
        if (!self::isValid($ip) || self::isPrivate($ip)) {
            return null;
        }
        
        // Example using ip-api.com (free for non-commercial use)
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
     * Check if IP address is from a specific country
     * @param string $ip IP address
     * @param string $countryCode 2-letter country code
     * @return bool True if IP is from specified country
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
     * Get IP address type (public/private/loopback)
     * @param string $ip IP address
     * @return string|null Type of IP address
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