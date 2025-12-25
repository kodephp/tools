<?php

namespace Kode\Math;

/**
 * 高精度数学计算工具类
 * 解决浮点数精度丢失问题
 * 支持加减乘除、四舍五入、比较等操作
 */
class Math
{
    /**
     * 加法运算
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function add(float|int|string $num1, float|int|string $num2, int $scale = 10): float
    {
        return bcadd((string)$num1, (string)$num2, $scale);
    }

    /**
     * 减法运算
     * @param float|int|string $num1 被减数
     * @param float|int|string $num2 减数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function sub(float|int|string $num1, float|int|string $num2, int $scale = 10): float
    {
        return bcsub((string)$num1, (string)$num2, $scale);
    }

    /**
     * 乘法运算
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function mul(float|int|string $num1, float|int|string $num2, int $scale = 10): float
    {
        return bcmul((string)$num1, (string)$num2, $scale);
    }

    /**
     * 除法运算
     * @param float|int|string $num1 被除数
     * @param float|int|string $num2 除数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function div(float|int|string $num1, float|int|string $num2, int $scale = 10): float
    {
        if ($num2 == 0) {
            throw new \InvalidArgumentException('除数不能为0');
        }
        return bcdiv((string)$num1, (string)$num2, $scale);
    }

    /**
     * 取模运算
     * @param float|int|string $num1 被除数
     * @param float|int|string $num2 除数
     * @return float 结果
     */
    public static function mod(float|int|string $num1, float|int|string $num2): float
    {
        if ($num2 == 0) {
            throw new \InvalidArgumentException('除数不能为0');
        }
        return bcmod((string)$num1, (string)$num2);
    }

    /**
     * 幂运算
     * @param float|int|string $num 底数
     * @param int $exponent 指数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function pow(float|int|string $num, int $exponent, int $scale = 10): float
    {
        return bcpow((string)$num, (string)$exponent, $scale);
    }

    /**
     * 平方根运算
     * @param float|int|string $num 被开方数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function sqrt(float|int|string $num, int $scale = 10): float
    {
        if ($num < 0) {
            throw new \InvalidArgumentException('被开方数不能为负数');
        }
        return bcsqrt((string)$num, $scale);
    }

    /**
     * 四舍五入
     * @param float|int|string $num 数值
     * @param int $precision 保留小数位数
     * @return float 结果
     */
    public static function round(float|int|string $num, int $precision = 0): float
    {
        $num = (string)$num;
        $scale = $precision + 1;
        $rounded = bcadd($num, '0.5' . str_repeat('0', $precision), $scale);
        return bcsub($rounded, '0.5' . str_repeat('0', $precision), $precision);
    }

    /**
     * 向上取整
     * @param float|int|string $num 数值
     * @param int $precision 保留小数位数
     * @return float 结果
     */
    public static function ceil(float|int|string $num, int $precision = 0): float
    {
        $num = (string)$num;
        $multiplier = pow(10, $precision);
        $scaled = bcadd($num, '0', $precision + 1);
        $integerPart = bcsub($scaled, bcmod($scaled, bcdiv('1', (string)$multiplier, $precision + 1), $precision + 1), $precision + 1);
        $fractionPart = bcmod($scaled, bcdiv('1', (string)$multiplier, $precision + 1), $precision + 1);

        if ($fractionPart > 0) {
            return bcadd($integerPart, bcdiv('1', (string)$multiplier, $precision), $precision);
        }

        return $integerPart;
    }

    /**
     * 向下取整
     * @param float|int|string $num 数值
     * @param int $precision 保留小数位数
     * @return float 结果
     */
    public static function floor(float|int|string $num, int $precision = 0): float
    {
        $num = (string)$num;
        $multiplier = pow(10, $precision);
        $scaled = bcadd($num, '0', $precision + 1);
        $integerPart = bcsub($scaled, bcmod($scaled, bcdiv('1', (string)$multiplier, $precision + 1), $precision + 1), $precision + 1);

        return $integerPart;
    }

    /**
     * 比较两个数的大小
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @return int 1表示num1>num2，0表示相等，-1表示num1<num2
     */
    public static function compare(float|int|string $num1, float|int|string $num2): int
    {
        return bccomp((string)$num1, (string)$num2);
    }

    /**
     * 判断两个数是否相等
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int $scale 比较精度
     * @return bool 是否相等
     */
    public static function equal(float|int|string $num1, float|int|string $num2, int $scale = 10): bool
    {
        return bccomp((string)$num1, (string)$num2, $scale) == 0;
    }

    /**
     * 格式化数字
     * @param float|int|string $num 数值
     * @param int $precision 保留小数位数
     * @param bool $thousandsSeparator 是否使用千分位分隔符
     * @return string 格式化后的字符串
     */
    public static function format(float|int|string $num, int $precision = 2, bool $thousandsSeparator = true): string
    {
        $num = (string)$num;
        $formatted = number_format((float)$num, $precision, '.', $thousandsSeparator ? ',' : '');
        return $formatted;
    }
}
