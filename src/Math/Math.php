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
     * @return string 结果
     */
    public static function add(float|int|string $num1, float|int|string $num2, int $scale = 10): string
    {
        return bcadd((string)$num1, (string)$num2, $scale);
    }

    /**
     * 减法运算
     * @param float|int|string $num1 被减数
     * @param float|int|string $num2 减数
     * @param int $scale 保留小数位数
     * @return string 结果
     */
    public static function sub(float|int|string $num1, float|int|string $num2, int $scale = 10): string
    {
        return bcsub((string)$num1, (string)$num2, $scale);
    }

    /**
     * 乘法运算
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int $scale 保留小数位数
     * @return string 结果
     */
    public static function mul(float|int|string $num1, float|int|string $num2, int $scale = 10): string
    {
        return bcmul((string)$num1, (string)$num2, $scale);
    }

    /**
     * 除法运算
     * @param float|int|string $num1 被除数
     * @param float|int|string $num2 除数
     * @param int $scale 保留小数位数
     * @return string 结果
     */
    public static function div(float|int|string $num1, float|int|string $num2, int $scale = 10): string
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
     * @return string 结果
     */
    public static function mod(float|int|string $num1, float|int|string $num2): string
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
     * @return string 结果
     */
    public static function pow(float|int|string $num, int $exponent, int $scale = 10): string
    {
        return bcpow((string)$num, (string)$exponent, $scale);
    }

    /**
     * 平方根运算
     * @param float|int|string $num 被开方数
     * @param int $scale 保留小数位数
     * @return string 结果
     */
    public static function sqrt(float|int|string $num, int $scale = 10): string
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
     * @return string 结果
     */
    public static function round(float|int|string $num, int $precision = 0): string
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
     * @return string 结果
     */
    public static function ceil(float|int|string $num, int $precision = 0): string
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
     * @return string 结果
     */
    public static function floor(float|int|string $num, int $precision = 0): string
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

    /**
     * 正弦函数
     * @param float|int|string $num 弧度值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function sin(float|int|string $num, int $scale = 10): float
    {
        return round(sin((float)$num), $scale);
    }

    /**
     * 余弦函数
     * @param float|int|string $num 弧度值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function cos(float|int|string $num, int $scale = 10): float
    {
        return round(cos((float)$num), $scale);
    }

    /**
     * 正切函数
     * @param float|int|string $num 弧度值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function tan(float|int|string $num, int $scale = 10): float
    {
        return round(tan((float)$num), $scale);
    }

    /**
     * 反正弦函数
     * @param float|int|string $num 数值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function asin(float|int|string $num, int $scale = 10): float
    {
        if ($num < -1 || $num > 1) {
            throw new \InvalidArgumentException('数值必须在[-1, 1]范围内');
        }
        return round(asin((float)$num), $scale);
    }

    /**
     * 反余弦函数
     * @param float|int|string $num 数值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function acos(float|int|string $num, int $scale = 10): float
    {
        if ($num < -1 || $num > 1) {
            throw new \InvalidArgumentException('数值必须在[-1, 1]范围内');
        }
        return round(acos((float)$num), $scale);
    }

    /**
     * 反正切函数
     * @param float|int|string $num 数值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function atan(float|int|string $num, int $scale = 10): float
    {
        return round(atan((float)$num), $scale);
    }

    /**
     * 自然对数
     * @param float|int|string $num 数值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function ln(float|int|string $num, int $scale = 10): float
    {
        if ($num <= 0) {
            throw new \InvalidArgumentException('数值必须大于0');
        }
        return round(log((float)$num), $scale);
    }

    /**
     * 常用对数
     * @param float|int|string $num 数值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function log10(float|int|string $num, int $scale = 10): float
    {
        if ($num <= 0) {
            throw new \InvalidArgumentException('数值必须大于0');
        }
        return round(log10((float)$num), $scale);
    }

    /**
     * 自定义底数对数
     * @param float|int|string $num 数值
     * @param float|int|string $base 底数
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function log(float|int|string $num, float|int|string $base, int $scale = 10): float
    {
        if ($num <= 0 || $base <= 0 || $base == 1) {
            throw new \InvalidArgumentException('数值必须大于0，底数必须大于0且不等于1');
        }
        return round(log((float)$num, (float)$base), $scale);
    }

    /**
     * 弧度转角度
     * @param float|int|string $num 弧度值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function rad2deg(float|int|string $num, int $scale = 10): float
    {
        return round(rad2deg((float)$num), $scale);
    }

    /**
     * 角度转弧度
     * @param float|int|string $num 角度值
     * @param int $scale 保留小数位数
     * @return float 结果
     */
    public static function deg2rad(float|int|string $num, int $scale = 10): float
    {
        return round(deg2rad((float)$num), $scale);
    }

    /**
     * 绝对值
     * @param float|int|string $num 数值
     * @return float 结果
     */
    public static function abs(float|int|string $num): float
    {
        return abs((float)$num);
    }

    /**
     * 阶乘
     * @param int $num 数值
     * @return int 结果
     */
    public static function factorial(int $num): int
    {
        if ($num < 0) {
            throw new \InvalidArgumentException('阶乘参数不能为负数');
        }
        if ($num <= 1) {
            return 1;
        }
        $result = 1;
        for ($i = 2; $i <= $num; $i++) {
            $result *= $i;
        }
        return $result;
    }

    /**
     * 最大公约数
     * @param int $num1 第一个数
     * @param int $num2 第二个数
     * @return int 结果
     */
    public static function gcd(int $num1, int $num2): int
    {
        return $num2 == 0 ? $num1 : self::gcd($num2, $num1 % $num2);
    }

    /**
     * 最小公倍数
     * @param int $num1 第一个数
     * @param int $num2 第二个数
     * @return int 结果
     */
    public static function lcm(int $num1, int $num2): int
    {
        if ($num1 == 0 || $num2 == 0) {
            return 0;
        }
        return abs($num1 * $num2) / self::gcd($num1, $num2);
    }

    /**
     * 百分比计算
     * @param float|int|string $part 部分数值
     * @param float|int|string $total 总数值
     * @param int $scale 保留小数位数
     * @return float 百分比值
     */
    public static function percentage(float|int|string $part, float|int|string $total, int $scale = 2): float
    {
        if ($total == 0) {
            throw new \InvalidArgumentException('总数值不能为0');
        }
        return self::mul(self::div($part, $total, $scale + 2), 100, $scale);
    }

    /**
     * 折扣计算
     * @param float|int|string $price 原价
     * @param float|int|string $discount 折扣（如0.8表示8折）
     * @param int $scale 保留小数位数
     * @return float 折后价格
     */
    public static function discount(float|int|string $price, float|int|string $discount, int $scale = 2): float
    {
        return self::mul($price, $discount, $scale);
    }

    /**
     * 税费计算
     * @param float|int|string $amount 金额
     * @param float|int|string $rate 税率（如0.1表示10%）
     * @param int $scale 保留小数位数
     * @return float 税费
     */
    public static function tax(float|int|string $amount, float|int|string $rate, int $scale = 2): float
    {
        return self::mul($amount, $rate, $scale);
    }

    /**
     * 含税金额计算
     * @param float|int|string $amount 不含税金额
     * @param float|int|string $rate 税率（如0.1表示10%）
     * @param int $scale 保留小数位数
     * @return float 含税金额
     */
    public static function taxIncluded(float|int|string $amount, float|int|string $rate, int $scale = 2): float
    {
        return self::add($amount, self::tax($amount, $rate, $scale), $scale);
    }

    /**
     * 不含税金额计算
     * @param float|int|string $amount 含税金额
     * @param float|int|string $rate 税率（如0.1表示10%）
     * @param int $scale 保留小数位数
     * @return float 不含税金额
     */
    public static function taxExcluded(float|int|string $amount, float|int|string $rate, int $scale = 2): float
    {
        return self::div($amount, self::add(1, $rate, $scale + 2), $scale);
    }

    /**
     * 简单利息计算
     * @param float|int|string $principal 本金
     * @param float|int|string $rate 年利率（如0.05表示5%）
     * @param int $years 年数
     * @param int $scale 保留小数位数
     * @return float 利息
     */
    public static function simpleInterest(float|int|string $principal, float|int|string $rate, int $years, int $scale = 2): float
    {
        return self::mul(self::mul($principal, $rate, $scale + 2), $years, $scale);
    }

    /**
     * 复利计算
     * @param float|int|string $principal 本金
     * @param float|int|string $rate 年利率（如0.05表示5%）
     * @param int $years 年数
     * @param int $scale 保留小数位数
     * @return float 本息合计
     */
    public static function compoundInterest(float|int|string $principal, float|int|string $rate, int $years, int $scale = 2): float
    {
        $factor = self::add(1, $rate, $scale + 2);
        $result = self::pow($factor, $years, $scale + 2);
        return self::mul($principal, $result, $scale);
    }

    /**
     * 生成指定范围内的随机数
     * @param float|int|string $min 最小值
     * @param float|int|string $max 最大值
     * @param int $scale 保留小数位数
     * @return float 随机数
     */
    public static function random(float|int|string $min, float|int|string $max, int $scale = 0): float
    {
        $random = mt_rand() / mt_getrandmax();
        $range = self::sub($max, $min, $scale + 2);
        return self::add($min, self::mul($range, $random, $scale + 2), $scale);
    }

    /**
     * 数值范围检查
     * @param float|int|string $num 数值
     * @param float|int|string $min 最小值
     * @param float|int|string $max 最大值
     * @return bool 是否在范围内
     */
    public static function inRange(float|int|string $num, float|int|string $min, float|int|string $max): bool
    {
        return self::compare($num, $min) >= 0 && self::compare($num, $max) <= 0;
    }

    /**
     * 限制数值范围
     * @param float|int|string $num 数值
     * @param float|int|string $min 最小值
     * @param float|int|string $max 最大值
     * @param int $scale 保留小数位数
     * @return float 限制后的数值
     */
    public static function clamp(float|int|string $num, float|int|string $min, float|int|string $max, int $scale = 10): float
    {
        if (self::compare($num, $min) < 0) {
            return (float)$min;
        }
        if (self::compare($num, $max) > 0) {
            return (float)$max;
        }
        return (float)$num;
    }

    /**
     * 判断是否为正数
     * @param float|int|string $num 数值
     * @return bool 是否为正数
     */
    public static function isPositive(float|int|string $num): bool
    {
        return self::compare($num, 0) > 0;
    }

    /**
     * 判断是否为负数
     * @param float|int|string $num 数值
     * @return bool 是否为负数
     */
    public static function isNegative(float|int|string $num): bool
    {
        return self::compare($num, 0) < 0;
    }

    /**
     * 判断是否为零
     * @param float|int|string $num 数值
     * @param int $scale 比较精度
     * @return bool 是否为零
     */
    public static function isZero(float|int|string $num, int $scale = 10): bool
    {
        return self::equal($num, 0, $scale);
    }

    /**
     * 判断是否为偶数
     * @param int $num 数值
     * @return bool 是否为偶数
     */
    public static function isEven(int $num): bool
    {
        return $num % 2 == 0;
    }

    /**
     * 判断是否为奇数
     * @param int $num 数值
     * @return bool 是否为奇数
     */
    public static function isOdd(int $num): bool
    {
        return $num % 2 != 0;
    }

    /**
     * 判断是否为质数
     * @param int $num 数值
     * @return bool 是否为质数
     */
    public static function isPrime(int $num): bool
    {
        if ($num < 2) {
            return false;
        }
        if ($num == 2) {
            return true;
        }
        if ($num % 2 == 0) {
            return false;
        }
        for ($i = 3; $i <= sqrt($num); $i += 2) {
            if ($num % $i == 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断数值是否有效
     * @param mixed $num 数值
     * @return bool 是否有效
     */
    public static function isValid(mixed $num): bool
    {
        return is_numeric($num) && !is_infinite((float)$num) && !is_nan((float)$num);
    }

    /**
     * 线性插值
     * @param float|int|string $start 起始值
     * @param float|int|string $end 结束值
     * @param float|int|string $t 插值因子（0-1）
     * @param int $scale 保留小数位数
     * @return float 插值结果
     */
    public static function lerp(float|int|string $start, float|int|string $end, float|int|string $t, int $scale = 10): float
    {
        $t = self::clamp($t, 0, 1, $scale);
        $diff = self::sub($end, $start, $scale + 2);
        return self::add($start, self::mul($diff, $t, $scale + 2), $scale);
    }

    /**
     * 平均值计算
     * @param array $numbers 数值数组
     * @param int $scale 保留小数位数
     * @return float 平均值
     */
    public static function average(array $numbers, int $scale = 10): float
    {
        if (empty($numbers)) {
            return 0;
        }
        $sum = 0;
        foreach ($numbers as $num) {
            $sum = self::add($sum, $num, $scale + 2);
        }
        return self::div($sum, count($numbers), $scale);
    }

    /**
     * 中位数计算
     * @param array $numbers 数值数组
     * @param int $scale 保留小数位数
     * @return float 中位数
     */
    public static function median(array $numbers, int $scale = 10): float
    {
        if (empty($numbers)) {
            return 0;
        }
        sort($numbers);
        $count = count($numbers);
        $middle = floor($count / 2);
        if ($count % 2 == 0) {
            return self::div(self::add($numbers[$middle - 1], $numbers[$middle], $scale + 2), 2, $scale);
        } else {
            return (float)$numbers[$middle];
        }
    }

    /**
     * 众数计算
     * @param array $numbers 数值数组
     * @return mixed 众数
     */
    public static function mode(array $numbers): mixed
    {
        if (empty($numbers)) {
            return null;
        }
        $counts = array_count_values($numbers);
        arsort($counts);
        return array_key_first($counts);
    }

    /**
     * 标准差计算
     * @param array $numbers 数值数组
     * @param int $scale 保留小数位数
     * @return float 标准差
     */
    public static function standardDeviation(array $numbers, int $scale = 10): float
    {
        if (count($numbers) < 2) {
            return 0;
        }
        $mean = self::average($numbers, $scale + 2);
        $sumSquares = 0;
        foreach ($numbers as $num) {
            $diff = self::sub($num, $mean, $scale + 2);
            $square = self::mul($diff, $diff, $scale + 2);
            $sumSquares = self::add($sumSquares, $square, $scale + 2);
        }
        $variance = self::div($sumSquares, count($numbers) - 1, $scale + 2);
        return self::sqrt($variance, $scale);
    }

    /**
     * 方差计算
     * @param array $numbers 数值数组
     * @param int $scale 保留小数位数
     * @return float 方差
     */
    public static function variance(array $numbers, int $scale = 10): float
    {
        if (count($numbers) < 2) {
            return 0;
        }
        $stdDev = self::standardDeviation($numbers, $scale + 2);
        return self::mul($stdDev, $stdDev, $scale);
    }
}
