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

    /**
     * 时间差（人类可读格式）
     * @param int|string $timestamp 时间戳或时间字符串
     * @param int|null $current 当前时间戳
     * @return string 时间差描述
     */
    public static function diffForHumans(int|string $timestamp, ?int $current = null): string
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $current = $current ?? time();
        $diff = $current - $timestamp;

        if ($diff < 0) {
            $diff = abs($diff);
            if ($diff < 60) {
                return $diff . '秒后';
            } elseif ($diff < 3600) {
                return floor($diff / 60) . '分钟后';
            } elseif ($diff < 86400) {
                return floor($diff / 3600) . '小时后';
            } elseif ($diff < 2592000) {
                return floor($diff / 86400) . '天后';
            } elseif ($diff < 31536000) {
                return floor($diff / 2592000) . '个月后';
            } else {
                return floor($diff / 31536000) . '年后';
            }
        } else {
            if ($diff < 60) {
                return $diff . '秒前';
            } elseif ($diff < 3600) {
                return floor($diff / 60) . '分钟前';
            } elseif ($diff < 86400) {
                return floor($diff / 3600) . '小时前';
            } elseif ($diff < 2592000) {
                return floor($diff / 86400) . '天前';
            } elseif ($diff < 31536000) {
                return floor($diff / 2592000) . '个月前';
            } else {
                return floor($diff / 31536000) . '年前';
            }
        }
    }

    /**
     * 获取本周开始时间
     * @param int $timestamp 时间戳
     * @return int 本周开始时间戳
     */
    public static function weekStart(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        $weekday = date('w', $timestamp);
        $weekday = $weekday == 0 ? 7 : $weekday;
        return strtotime(date('Y-m-d', $timestamp - ($weekday - 1) * 86400) . ' 00:00:00');
    }

    /**
     * 获取本周结束时间
     * @param int $timestamp 时间戳
     * @return int 本周结束时间戳
     */
    public static function weekEnd(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        $weekday = date('w', $timestamp);
        $weekday = $weekday == 0 ? 7 : $weekday;
        return strtotime(date('Y-m-d', $timestamp + (7 - $weekday) * 86400) . ' 23:59:59');
    }

    /**
     * 获取本月开始时间
     * @param int $timestamp 时间戳
     * @return int 本月开始时间戳
     */
    public static function monthStart(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-m-01 00:00:00', $timestamp));
    }

    /**
     * 获取本月结束时间
     * @param int $timestamp 时间戳
     * @return int 本月结束时间戳
     */
    public static function monthEnd(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-m-t 23:59:59', $timestamp));
    }

    /**
     * 获取本年开始时间
     * @param int $timestamp 时间戳
     * @return int 本年开始时间戳
     */
    public static function yearStart(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-01-01 00:00:00', $timestamp));
    }

    /**
     * 获取本年结束时间
     * @param int $timestamp 时间戳
     * @return int 本年结束时间戳
     */
    public static function yearEnd(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-12-31 23:59:59', $timestamp));
    }

    /**
     * 获取上周开始时间
     * @param int $timestamp 时间戳
     * @return int 上周开始时间戳
     */
    public static function lastWeekStart(int $timestamp = null): int
    {
        return self::weekStart($timestamp) - 7 * 86400;
    }

    /**
     * 获取上周结束时间
     * @param int $timestamp 时间戳
     * @return int 上周结束时间戳
     */
    public static function lastWeekEnd(int $timestamp = null): int
    {
        return self::weekEnd($timestamp) - 7 * 86400;
    }

    /**
     * 获取上月开始时间
     * @param int $timestamp 时间戳
     * @return int 上月开始时间戳
     */
    public static function lastMonthStart(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-m-01 00:00:00', strtotime('-1 month', $timestamp)));
    }

    /**
     * 获取上月结束时间
     * @param int $timestamp 时间戳
     * @return int 上月结束时间戳
     */
    public static function lastMonthEnd(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime(date('Y-m-t 23:59:59', strtotime('-1 month', $timestamp)));
    }

    /**
     * 获取上年开始时间
     * @param int $timestamp 时间戳
     * @return int 上年开始时间戳
     */
    public static function lastYearStart(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime((date('Y', $timestamp) - 1) . '-01-01 00:00:00');
    }

    /**
     * 获取上年结束时间
     * @param int $timestamp 时间戳
     * @return int 上年结束时间戳
     */
    public static function lastYearEnd(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return strtotime((date('Y', $timestamp) - 1) . '-12-31 23:59:59');
    }

    /**
     * 判断是否在某个时间区间内
     * @param int $timestamp 时间戳
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     * @return bool 是否在区间内
     */
    public static function between(int $timestamp, int $start, int $end): bool
    {
        return $timestamp >= $start && $timestamp <= $end;
    }

    /**
     * 判断是否是今天
     * @param int $timestamp 时间戳
     * @return bool 是否是今天
     */
    public static function isToday(int $timestamp): bool
    {
        return date('Y-m-d', $timestamp) == date('Y-m-d');
    }

    /**
     * 判断是否是昨天
     * @param int $timestamp 时间戳
     * @return bool 是否是昨天
     */
    public static function isYesterday(int $timestamp): bool
    {
        return date('Y-m-d', $timestamp) == date('Y-m-d', time() - 86400);
    }

    /**
     * 判断是否是明天
     * @param int $timestamp 时间戳
     * @return bool 是否是明天
     */
    public static function isTomorrow(int $timestamp): bool
    {
        return date('Y-m-d', $timestamp) == date('Y-m-d', time() + 86400);
    }

    /**
     * 判断是否是本周
     * @param int $timestamp 时间戳
     * @return bool 是否是本周
     */
    public static function isThisWeek(int $timestamp): bool
    {
        return $timestamp >= self::weekStart() && $timestamp <= self::weekEnd();
    }

    /**
     * 判断是否是本月
     * @param int $timestamp 时间戳
     * @return bool 是否是本月
     */
    public static function isThisMonth(int $timestamp): bool
    {
        return date('Y-m', $timestamp) == date('Y-m');
    }

    /**
     * 判断是否是本年
     * @param int $timestamp 时间戳
     * @return bool 是否是本年
     */
    public static function isThisYear(int $timestamp): bool
    {
        return date('Y', $timestamp) == date('Y');
    }

    /**
     * 获取某个月的天数
     * @param int $month 月份
     * @param int $year 年份
     * @return int 天数
     */
    public static function daysInMonth(int $month, int $year = null): int
    {
        $year = $year ?? (int)date('Y');
        return (int)date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    /**
     * 获取某天是周几
     * @param int $timestamp 时间戳
     * @return int 周几（0-6，0表示周日）
     */
    public static function dayOfWeek(int $timestamp): int
    {
        return (int)date('w', $timestamp);
    }

    /**
     * 获取某天是周几（中文名称）
     * @param int $timestamp 时间戳
     * @return string 周几
     */
    public static function dayOfWeekName(int $timestamp): string
    {
        $weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
        return $weekdays[self::dayOfWeek($timestamp)];
    }

    /**
     * 获取某天是本年第几天
     * @param int $timestamp 时间戳
     * @return int 第几天
     */
    public static function dayOfYear(int $timestamp): int
    {
        return (int)date('z', $timestamp) + 1;
    }

    /**
     * 获取某天是本年第几周
     * @param int $timestamp 时间戳
     * @return int 第几周
     */
    public static function weekOfYear(int $timestamp): int
    {
        return (int)date('W', $timestamp);
    }

    /**
     * 计算年龄
     * @param string $birthday 生日（格式：Y-m-d）
     * @return int 年龄
     */
    public static function age(string $birthday): int
    {
        $birthdayTimestamp = strtotime($birthday);
        $age = date('Y') - date('Y', $birthdayTimestamp);
        if (date('md') < date('md', $birthdayTimestamp)) {
            $age--;
        }
        return $age;
    }

    /**
     * 时间字符串转时间戳
     * @param string $timeStr 时间字符串
     * @return int 时间戳
     */
    public static function toTimestamp(string $timeStr): int
    {
        return strtotime($timeStr);
    }

    /**
     * 时间戳转毫秒
     * @param int $timestamp 时间戳
     * @return int 毫秒时间戳
     */
    public static function toMillisecond(int $timestamp = null): int
    {
        $timestamp = $timestamp ?? time();
        return $timestamp * 1000;
    }

    /**
     * 毫秒转时间戳
     * @param int $millisecond 毫秒时间戳
     * @return int 时间戳
     */
    public static function fromMillisecond(int $millisecond): int
    {
        return (int)($millisecond / 1000);
    }

    /**
     * 获取当前毫秒时间戳
     * @return int 毫秒时间戳
     */
    public static function millisecond(): int
    {
        return (int)(microtime(true) * 1000);
    }

    /**
     * 获取当前微秒时间戳
     * @return int 微秒时间戳
     */
    public static function microsecond(): int
    {
        return (int)(microtime(true) * 1000000);
    }

    /**
     * 获取当前时间戳（带微秒）
     * @return float 时间戳
     */
    public static function microtime(): float
    {
        return microtime(true);
    }

    /**
     * 获取时区
     * @return string 时区
     */
    public static function timezone(): string
    {
        return date_default_timezone_get();
    }

    /**
     * 设置时区
     * @param string $timezone 时区
     * @return bool 是否成功
     */
    public static function setTimezone(string $timezone): bool
    {
        return date_default_timezone_set($timezone);
    }
}
