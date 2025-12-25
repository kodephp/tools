<?php

namespace Kode\Time;

class Time
{
    /**
     * 格式化时间
     * @param int|null $timestamp 时间戳
     * @param string $format 格式
     * @return string 格式化后的时间
     */
    public static function format(?int $timestamp = null, string $format = 'Y-m-d H:i:s'): string
    {
        $timestamp = $timestamp ?? time();
        return date($format, $timestamp);
    }

    /**
     * 获取当前时间
     * @param string $format 格式
     * @return string 当前时间
     */
    public static function now(string $format = 'Y-m-d H:i:s'): string
    {
        return self::format(time(), $format);
    }

    /**
     * 获取今天日期
     * @param string $format 格式
     * @return string 今天日期
     */
    public static function today(string $format = 'Y-m-d'): string
    {
        return self::format(time(), $format);
    }

    /**
     * 获取昨天日期
     * @param string $format 格式
     * @return string 昨天日期
     */
    public static function yesterday(string $format = 'Y-m-d'): string
    {
        return self::format(time() - 86400, $format);
    }

    /**
     * 获取明天日期
     * @param string $format 格式
     * @return string 明天日期
     */
    public static function tomorrow(string $format = 'Y-m-d'): string
    {
        return self::format(time() + 86400, $format);
    }

    /**
     * 时间加法
     * @param int $timestamp 时间戳
     * @param int $interval 间隔（秒）
     * @return int 新时间戳
     */
    public static function add(int $timestamp, int $interval): int
    {
        return $timestamp + $interval;
    }

    /**
     * 时间减法
     * @param int $timestamp 时间戳
     * @param int $interval 间隔（秒）
     * @return int 新时间戳
     */
    public static function sub(int $timestamp, int $interval): int
    {
        return $timestamp - $interval;
    }

    /**
     * 时间差
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     * @return int 时间差（秒）
     */
    public static function diff(int $start, int $end): int
    {
        return abs($end - $start);
    }
}
