<?php

namespace Kode\Geo;

/**
 * 地理位置工具类
 * 提供坐标之间的距离计算和位置相关操作
 */
class Geo
{
    /**
     * 地球半径（公里，WGS84椭球）
     */
    private const EARTH_RADIUS_KM = 6378.137;
    
    /**
     * 地球半径（英里）
     */
    private const EARTH_RADIUS_MI = 3963.191;
    
    /**
     * 地球半径（米）
     */
    private const EARTH_RADIUS_M = 6378137;

    /**
     * 使用Haversine公式计算两个坐标之间的距离
     * @param float $lat1 第一个点的纬度
     * @param float $lon1 第一个点的经度
     * @param float $lat2 第二个点的纬度
     * @param float $lon2 第二个点的经度
     * @param string $unit 单位（km:公里, mi:英里, m:米）
     * @return float 两点之间的距离
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'km'): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
             
        return match($unit) {
            'mi' => $c * self::EARTH_RADIUS_MI,
            'm' => $c * self::EARTH_RADIUS_M,
            default => $c * self::EARTH_RADIUS_KM
        };
    }

    /**
     * 将角度转换为弧度
     * @param float $degrees 角度值
     * @return float 弧度值
     */
    public static function toRadians(float $degrees): float
    {
        return deg2rad($degrees);
    }

    /**
     * 将弧度转换为角度
     * @param float $radians 弧度值
     * @return float 角度值
     */
    public static function toDegrees(float $radians): float
    {
        return rad2deg($radians);
    }

    /**
     * 验证坐标是否有效
     * @param float $lat 纬度
     * @param float $lon 经度
     * @return bool 如果坐标有效则返回true
     */
    public static function isValid(float $lat, float $lon): bool
    {
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }

    /**
     * 计算两个坐标之间的中点
     * @param float $lat1 第一个点的纬度
     * @param float $lon1 第一个点的经度
     * @param float $lat2 第二个点的纬度
     * @param float $lon2 第二个点的经度
     * @return array 中点坐标 [纬度, 经度]
     */
    public static function midpoint(float $lat1, float $lon1, float $lat2, float $lon2): array
    {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $bx = cos($lat2Rad) * cos($lon2Rad - $lon1Rad);
        $by = cos($lat2Rad) * sin($lon2Rad - $lon1Rad);
        
        $lat3Rad = atan2(sin($lat1Rad) + sin($lat2Rad), sqrt((cos($lat1Rad) + $bx) * (cos($lat1Rad) + $bx) + $by * $by));
        $lon3Rad = $lon1Rad + atan2($by, cos($lat1Rad) + $bx);
        
        return [rad2deg($lat3Rad), rad2deg($lon3Rad)];
    }

    /**
     * 计算两个坐标之间的方位角
     * @param float $lat1 第一个点的纬度
     * @param float $lon1 第一个点的经度
     * @param float $lat2 第二个点的纬度
     * @param float $lon2 第二个点的经度
     * @return float 方位角（0-360度）
     */
    public static function bearing(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $dLon = $lon2Rad - $lon1Rad;
        
        $y = sin($dLon) * cos($lat2Rad);
        $x = cos($lat1Rad) * sin($lat2Rad) - sin($lat1Rad) * cos($lat2Rad) * cos($dLon);
        
        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);
        $bearing = ($bearing + 360) % 360;
        
        return $bearing;
    }

    /**
     * 将十进制度数转换为度分秒（DMS）
     * @param float $decimal 十进制度数
     * @param bool $isLatitude 是否为纬度
     * @return array DMS组件 [度, 分, 秒, 方向]
     */
    public static function toDMS(float $decimal, bool $isLatitude): array
    {
        $direction = match(true) {
            $isLatitude && $decimal >= 0 => 'N',
            $isLatitude && $decimal < 0 => 'S',
            !$isLatitude && $decimal >= 0 => 'E',
            default => 'W'
        };
        
        $absDecimal = abs($decimal);
        $degrees = (int)$absDecimal;
        $minutes = ($absDecimal - $degrees) * 60;
        $seconds = ($minutes - (int)$minutes) * 60;
        
        return [$degrees, (int)$minutes, round($seconds, 4), $direction];
    }

    /**
     * 将度分秒（DMS）转换为十进制度数
     * @param int $degrees 度
     * @param int $minutes 分
     * @param float $seconds 秒
     * @param string $direction 方向（N, S, E, W）
     * @return float 十进制度数
     */
    public static function toDecimal(int $degrees, int $minutes, float $seconds, string $direction): float
    {
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        return in_array(strtoupper($direction), ['S', 'W']) ? -$decimal : $decimal;
    }
}