<?php

namespace Kode\String;

class Str
{
    /**
     * 生成UUID
     * @return string UUID
     */
    public static function uuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
    }

    /**
     * 大驼峰命名
     * @param string $str 字符串
     * @return string 大驼峰命名
     */
    public static function studly(string $str): string
    {
        $str = ucwords(str_replace(['-', '_', '.'], ' ', $str));
        return str_replace(' ', '', $str);
    }

    /**
     * 自定义字符串清理
     * @param string $str 字符串
     * @param array $options 清理选项
     * @return string 清理后的字符串
     */
    public static function clean(string $str, array $options = []): string
    {
        $defaults = [
            'trim' => true,
            'strip_tags' => true,
            'normalize_whitespace' => true,
            'remove_control_chars' => true,
            'to_lower' => false,
            'to_upper' => false,
        ];

        $options = array_merge($defaults, $options);

        if ($options['strip_tags']) {
            $str = strip_tags($str);
        }

        if ($options['remove_control_chars']) {
            $str = preg_replace('/[\x00-\x1F\x7F]/', '', $str);
        }

        if ($options['normalize_whitespace']) {
            $str = preg_replace('/\s+/', ' ', $str);
        }

        if ($options['trim']) {
            $str = trim($str);
        }

        if ($options['to_lower']) {
            $str = strtolower($str);
        }

        if ($options['to_upper']) {
            $str = strtoupper($str);
        }

        return $str;
    }

    /**
     * 字符串相似度比较
     * @param string $str1 字符串1
     * @param string $str2 字符串2
     * @return float 相似度（0-1）
     */
    public static function similarity(string $str1, string $str2): float
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        $max_len = max($len1, $len2);

        if ($max_len === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $max_len);
    }

    /**
     * 生成随机字符串（支持多种模式）
     * @param int $length 长度
     * @param string $mode 模式（alnum, alpha, numeric, hex, binary, special）
     * @return string 随机字符串
     */
    public static function random(int $length = 16, string $mode = 'alnum'): string
    {
        $chars = [
            'alnum' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            'numeric' => '0123456789',
            'hex' => '0123456789abcdef',
            'binary' => '01',
            'special' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
        ];

        $char_set = $chars[$mode] ?? $chars['alnum'];
        $random = '';
        $charsLength = strlen($char_set);

        for ($i = 0; $i < $length; $i++) {
            $random .= $char_set[random_int(0, $charsLength - 1)];
        }

        return $random;
    }

    /**
     * 字符串格式化（类似Python的str.format）
     * @param string $str 模板字符串
     * @param array $data 数据
     * @return string 格式化后的字符串
     */
    public static function format(string $str, array $data): string
    {
        $replace = [];
        foreach ($data as $key => $value) {
            $replace['{' . $key . '}'] = $value;
        }
        return str_replace(array_keys($replace), array_values($replace), $str);
    }

    /**
     * 字符串分割（支持多种分隔符）
     * @param string $str 字符串
     * @param string|array $delimiters 分隔符
     * @return array 分割后的数组
     */
    public static function split(string $str, string|array $delimiters): array
    {
        if (is_array($delimiters)) {
            $delimiters = array_map(function ($delimiter) {
                return preg_quote($delimiter, '/');
            }, $delimiters);
            $pattern = '/(' . implode('|', $delimiters) . ')/';
        } else {
            $pattern = '/' . preg_quote($delimiters, '/') . '/';
        }

        return preg_split($pattern, $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * 字符串截断（支持中文）
     * @param string $str 字符串
     * @param int $length 长度
     * @param string $suffix 后缀
     * @param string $encoding 编码
     * @return string 截断后的字符串
     */
    public static function cut(string $str, int $length, string $suffix = '...', string $encoding = 'UTF-8'): string
    {
        if (mb_strlen($str, $encoding) <= $length) {
            return $str;
        }
        return mb_substr($str, 0, $length, $encoding) . $suffix;
    }

    /**
     * 驼峰命名转换
     * @param string $str 字符串
     * @param string $separator 分隔符
     * @return string 驼峰命名
     */
    public static function camel(string $str, string $separator = '_'): string
    {
        $str = ucwords(str_replace($separator, ' ', $str));
        $str = str_replace(' ', '', lcfirst($str));
        return $str;
    }

    /**
     * 蛇形命名转换
     * @param string $str 字符串
     * @param string $separator 分隔符
     * @return string 蛇形命名
     */
    public static function snake(string $str, string $separator = '_'): string
    {
        $str = preg_replace('/(?<!^)[A-Z]/', $separator . '$0', $str);
        return strtolower($str);
    }

    /**
     * 字符串反转
     * @param string $str 字符串
     * @param string $encoding 编码
     * @return string 反转后的字符串
     */
    public static function reverse(string $str, string $encoding = 'UTF-8'): string
    {
        $chars = mb_str_split($str, 1, $encoding);
        return implode('', array_reverse($chars));
    }

    /**
     * 字符串重复
     * @param string $str 字符串
     * @param int $times 重复次数
     * @param string $separator 分隔符
     * @return string 重复后的字符串
     */
    public static function repeat(string $str, int $times, string $separator = ''): string
    {
        $result = [];
        for ($i = 0; $i < $times; $i++) {
            $result[] = $str;
        }
        return implode($separator, $result);
    }

    /**
     * 字符串填充
     * @param string $str 字符串
     * @param int $length 总长度
     * @param string $pad 填充字符
     * @param int $type 填充类型（STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH）
     * @return string 填充后的字符串
     */
    public static function pad(string $str, int $length, string $pad = ' ', int $type = STR_PAD_RIGHT): string
    {
        return str_pad($str, $length, $pad, $type);
    }

    /**
     * 字符串脱敏
     * @param string $str 字符串
     * @param int $start 开始位置
     * @param int $length 脱敏长度
     * @param string $mask 脱敏字符
     * @return string 脱敏后的字符串
     */
    public static function mask(string $str, int $start = 0, int $length = -1, string $mask = '*'): string
    {
        $str_length = strlen($str);
        if ($start >= $str_length) {
            return $str;
        }
        if ($length === -1) {
            $length = $str_length - $start;
        }
        $end = $start + $length;
        if ($end > $str_length) {
            $end = $str_length;
        }
        $mask_length = $end - $start;
        $mask_str = str_repeat($mask, $mask_length);
        return substr_replace($str, $mask_str, $start, $mask_length);
    }

    /**
     * 手机号脱敏
     * @param string $phone 手机号
     * @param int $keepStart 保留前几位
     * @param int $keepEnd 保留后几位
     * @return string 脱敏后的手机号
     */
    public static function maskPhone(string $phone, int $keepStart = 3, int $keepEnd = 4): string
    {
        $length = strlen($phone);
        if ($length <= $keepStart + $keepEnd) {
            return $phone;
        }
        return self::mask($phone, $keepStart, $length - $keepStart - $keepEnd);
    }

    /**
     * 身份证号脱敏
     * @param string $idCard 身份证号
     * @param int $keepStart 保留前几位
     * @param int $keepEnd 保留后几位
     * @return string 脱敏后的身份证号
     */
    public static function maskIdCard(string $idCard, int $keepStart = 6, int $keepEnd = 4): string
    {
        $length = strlen($idCard);
        if ($length <= $keepStart + $keepEnd) {
            return $idCard;
        }
        return self::mask($idCard, $keepStart, $length - $keepStart - $keepEnd);
    }

    /**
     * 邮箱脱敏
     * @param string $email 邮箱
     * @param int $keepChars 保留字符数
     * @return string 脱敏后的邮箱
     */
    public static function maskEmail(string $email, int $keepChars = 2): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }
        $username = $parts[0];
        $domain = $parts[1];
        $usernameLength = strlen($username);
        if ($usernameLength <= $keepChars) {
            return $email;
        }
        $maskLength = $usernameLength - $keepChars;
        $maskStr = str_repeat('*', $maskLength);
        return substr($username, 0, $keepChars) . $maskStr . '@' . $domain;
    }

    /**
     * 字符串转URL编码
     * @param string $str 字符串
     * @param string $encoding 编码
     * @return string URL编码后的字符串
     */
    public static function urlEncode(string $str, string $encoding = 'UTF-8'): string
    {
        return rawurlencode($str);
    }

    /**
     * URL编码转字符串
     * @param string $str URL编码后的字符串
     * @return string 解码后的字符串
     */
    public static function urlDecode(string $str): string
    {
        return rawurldecode($str);
    }

    /**
     * 字符串转Base64编码
     * @param string $str 字符串
     * @param string $encoding 编码
     * @return string Base64编码后的字符串
     */
    public static function base64Encode(string $str, string $encoding = 'UTF-8'): string
    {
        return base64_encode($str);
    }

    /**
     * Base64编码转字符串
     * @param string $str Base64编码后的字符串
     * @param string $encoding 编码
     * @return string 解码后的字符串
     */
    public static function base64Decode(string $str, string $encoding = 'UTF-8'): string
    {
        $decoded = base64_decode($str);
        return mb_convert_encoding($decoded, $encoding, 'UTF-8');
    }

    /**
     * 字符串转JSON
     * @param mixed $data 数据
     * @param int $options JSON选项
     * @return string JSON字符串
     */
    public static function toJson(mixed $data, int $options = 0): string
    {
        return json_encode($data, $options);
    }

    /**
     * JSON转字符串
     * @param string $json JSON字符串
     * @param bool $assoc 是否返回数组
     * @return mixed 解码后的数据
     */
    public static function fromJson(string $json, bool $assoc = false): mixed
    {
        return json_decode($json, $assoc);
    }

    /**
     * 字符串转XML
     * @param array $data 数据
     * @param string $root 根节点
     * @return string XML字符串
     */
    public static function toXml(array $data, string $root = 'root'): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<' . $root . '>';
        foreach ($data as $key => $value) {
            $xml .= '<' . $key . '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</' . $key . '>';
        }
        $xml .= '</' . $root . '>';
        return $xml;
    }

    /**
     * XML转字符串
     * @param string $xml XML字符串
     * @return array 解码后的数据
     */
    public static function fromXml(string $xml): array
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xml), true);
    }

    /**
     * 字符串转数组
     * @param string $str 字符串
     * @param string $delimiter 分隔符
     * @param string $enclosure 包围符
     * @return array 数组
     */
    public static function toArray(string $str, string $delimiter = ',', string $enclosure = '"'): array
    {
        $result = [];
        $str = trim($str);
        if (empty($str)) {
            return $result;
        }
        $pattern = '/(' . preg_quote($enclosure, '/') . '([^' . preg_quote($enclosure, '/') . ']*)' . preg_quote($enclosure, '/') . '|([^' . preg_quote($delimiter, '/') . ']+))/';
        preg_match_all($pattern, $str, $matches);
        foreach ($matches[0] as $match) {
            $match = trim($match);
            if (str_starts_with($match, $enclosure) && str_ends_with($match, $enclosure)) {
                $match = substr($match, 1, -1);
            }
            $result[] = $match;
        }
        return $result;
    }

    /**
     * 数组转字符串
     * @param array $array 数组
     * @param string $delimiter 分隔符
     * @param string $enclosure 包围符
     * @return string 字符串
     */
    public static function fromArray(array $array, string $delimiter = ',', string $enclosure = '"'): string
    {
        $result = [];
        foreach ($array as $value) {
            if (str_contains($value, $delimiter) || str_contains($value, $enclosure)) {
                $result[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $value) . $enclosure;
            } else {
                $result[] = $value;
            }
        }
        return implode($delimiter, $result);
    }

    /**
     * 字符串转布尔值
     * @param string $str 字符串
     * @return bool 布尔值
     */
    public static function toBool(string $str): bool
    {
        $str = strtolower(trim($str));
        return in_array($str, ['true', '1', 'yes', 'on']);
    }

    /**
     * 字符串转整数
     * @param string $str 字符串
     * @return int 整数
     */
    public static function toInt(string $str): int
    {
        return (int)$str;
    }

    /**
     * 字符串转浮点数
     * @param string $str 字符串
     * @return float 浮点数
     */
    public static function toFloat(string $str): float
    {
        return (float)$str;
    }

    /**
     * 字符串转日期
     * @param string $str 字符串
     * @param string $format 格式
     * @return \DateTimeInterface 日期对象
     */
    public static function toDate(string $str, string $format = 'Y-m-d H:i:s'): \DateTimeInterface
    {
        $date = \DateTime::createFromFormat($format, $str);
        if (!$date) {
            throw new \InvalidArgumentException("Invalid date format: {$str}");
        }
        return $date;
    }

    /**
     * 日期转字符串
     * @param \DateTimeInterface $date 日期对象
     * @param string $format 格式
     * @return string 字符串
     */
    public static function fromDate(\DateTimeInterface $date, string $format = 'Y-m-d H:i:s'): string
    {
        return $date->format($format);
    }

    /**
     * 字符串转IP地址
     * @param string $str 字符串
     * @return string IP地址
     */
    public static function toIp(string $str): string
    {
        $str = trim($str);
        if (filter_var($str, FILTER_VALIDATE_IP)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转邮箱地址
     * @param string $str 字符串
     * @return string 邮箱地址
     */
    public static function toEmail(string $str): string
    {
        $str = trim($str);
        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转URL地址
     * @param string $str 字符串
     * @return string URL地址
     */
    public static function toUrl(string $str): string
    {
        $str = trim($str);
        if (filter_var($str, FILTER_VALIDATE_URL)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转手机号码
     * @param string $str 字符串
     * @return string 手机号码
     */
    public static function toPhone(string $str): string
    {
        $str = preg_replace('/[^0-9]/', '', $str);
        if (preg_match('/^1[3-9]\d{9}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转身份证号
     * @param string $str 字符串
     * @return string 身份证号
     */
    public static function toIdCard(string $str): string
    {
        $str = preg_replace('/[^0-9Xx]/', '', $str);
        if (preg_match('/^\d{17}[0-9Xx]$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转银行卡号
     * @param string $str 字符串
     * @return string 银行卡号
     */
    public static function toBankCard(string $str): string
    {
        $str = preg_replace('/[^0-9]/', '', $str);
        if (preg_match('/^\d{16,19}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转车牌号
     * @param string $str 字符串
     * @return string 车牌号
     */
    public static function toCarPlate(string $str): string
    {
        $str = trim($str);
        if (preg_match('/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转邮编
     * @param string $str 字符串
     * @return string 邮编
     */
    public static function toPostCode(string $str): string
    {
        $str = preg_replace('/[^0-9]/', '', $str);
        if (preg_match('/^\d{6}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转QQ号
     * @param string $str 字符串
     * @return string QQ号
     */
    public static function toQq(string $str): string
    {
        $str = preg_replace('/[^0-9]/', '', $str);
        if (preg_match('/^[1-9]\d{4,10}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转微信号
     * @param string $str 字符串
     * @return string 微信号
     */
    public static function toWechat(string $str): string
    {
        $str = trim($str);
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{5,19}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 解析收货地址
     * @param string $address 收货地址
     * @return array 解析后的地址信息
     */
    public static function parseAddress(string $address): array
    {
        $result = [
            'province' => '',
            'city' => '',
            'district' => '',
            'street' => '',
            'detail' => '',
            'zip' => '',
            'phone' => '',
            'name' => ''
        ];

        // 提取手机号
        if (preg_match('/1[3-9]\d{9}/', $address, $matches)) {
            $result['phone'] = $matches[0];
            $address = str_replace($matches[0], '', $address);
        }

        // 提取邮编
        if (preg_match('/\d{6}/', $address, $matches)) {
            $result['zip'] = $matches[0];
            $address = str_replace($matches[0], '', $address);
        }

        // 提取姓名
        if (preg_match('/([\u4e00-\u9fa5]{2,8})/', $address, $matches)) {
            $result['name'] = $matches[0];
            $address = str_replace($matches[0], '', $address);
        }

        // 提取省市区
        $provinces = ['北京市', '上海市', '天津市', '重庆市', '河北省', '山西省', '辽宁省', '吉林省', '黑龙江省', '江苏省', '浙江省', '安徽省', '福建省', '江西省', '山东省', '河南省', '湖北省', '湖南省', '广东省', '海南省', '四川省', '贵州省', '云南省', '陕西省', '甘肃省', '青海省', '台湾省', '内蒙古自治区', '广西壮族自治区', '西藏自治区', '宁夏回族自治区', '新疆维吾尔自治区', '香港特别行政区', '澳门特别行政区'];
        foreach ($provinces as $province) {
            if (str_contains($address, $province)) {
                $result['province'] = $province;
                $address = str_replace($province, '', $address);
                break;
            }
        }

        // 提取城市
        $cities = ['市', '地区', '自治州', '盟'];
        foreach ($cities as $city_suffix) {
            if (str_contains($address, $city_suffix)) {
                $parts = explode($city_suffix, $address, 2);
                $result['city'] = $parts[0] . $city_suffix;
                $address = $parts[1] ?? '';
                break;
            }
        }

        // 提取区县
        $districts = ['区', '县', '自治县', '旗', '自治旗', '特区', '林区'];
        foreach ($districts as $district_suffix) {
            if (str_contains($address, $district_suffix)) {
                $parts = explode($district_suffix, $address, 2);
                $result['district'] = $parts[0] . $district_suffix;
                $address = $parts[1] ?? '';
                break;
            }
        }

        // 剩余部分作为详细地址
        $result['detail'] = trim($address);

        return $result;
    }

    /**
     * 验证手机号（支持国际号码）
     * @param string $phone 手机号
     * @param string $region 地区代码（如CN, US）
     * @return bool 是否有效
     */
    public static function validatePhone(string $phone, string $region = 'CN'): bool
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        switch ($region) {
            case 'CN':
                return preg_match('/^1[3-9]\d{9}$/', $phone) === 1;
            case 'US':
                return preg_match('/^\+?1\d{10}$/', $phone) === 1;
            case 'JP':
                return preg_match('/^\+?81\d{10,11}$/', $phone) === 1;
            default:
                return filter_var($phone, FILTER_VALIDATE_INT) !== false;
        }
    }

    /**
     * 验证邮箱（支持MX记录检查）
     * @param string $email 邮箱
     * @param bool $checkMx 是否检查MX记录
     * @return bool 是否有效
     */
    public static function validateEmail(string $email, bool $checkMx = false): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($checkMx) {
            $parts = explode('@', $email);
            $domain = $parts[1] ?? '';
            return checkdnsrr($domain, 'MX');
        }

        return true;
    }

    /**
     * 验证身份证号（支持15位和18位）
     * @param string $idCard 身份证号
     * @return bool 是否有效
     */
    public static function validateIdCard(string $idCard): bool
    {
        $idCard = preg_replace('/[^0-9Xx]/', '', $idCard);
        $length = strlen($idCard);

        if ($length === 15) {
            return preg_match('/^\d{15}$/', $idCard) === 1;
        } elseif ($length === 18) {
            if (!preg_match('/^\d{17}[0-9Xx]$/', $idCard)) {
                return false;
            }

            // 校验码验证
            $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $checkCodes = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            $sum = 0;

            for ($i = 0; $i < 17; $i++) {
                $sum += (int)$idCard[$i] * $weights[$i];
            }

            $remainder = $sum % 11;
            $checkCode = strtoupper($idCard[17]);

            return $checkCode === $checkCodes[$remainder];
        }

        return false;
    }

    /**
     * 验证车牌号（支持新能源和特殊车辆）
     * @param string $plate 车牌号
     * @return bool 是否有效
     */
    public static function validateCarPlate(string $plate): bool
    {
        $plate = trim(strtoupper($plate));

        // 普通蓝牌
        if (preg_match('/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/', $plate)) {
            return true;
        }

        // 新能源车牌
        if (preg_match('/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}(([0-9]{5}[DF])|([DF][0-9]{5}))$/', $plate)) {
            return true;
        }

        // 使馆车牌
        if (preg_match('/^[使领]{1}[A-Z0-9]{4}[A-Z0-9]{1}$/', $plate)) {
            return true;
        }

        return false;
    }

    /**
     * 银行卡号脱敏
     * @param string $bankCard 银行卡号
     * @param int $keepStart 保留前几位
     * @param int $keepEnd 保留后几位
     * @return string 脱敏后的银行卡号
     */
    public static function maskBankCard(string $bankCard, int $keepStart = 6, int $keepEnd = 4): string
    {
        $length = strlen($bankCard);
        if ($length <= $keepStart + $keepEnd) {
            return $bankCard;
        }
        return self::mask($bankCard, $keepStart, $length - $keepStart - $keepEnd);
    }

    /**
     * 车牌号脱敏
     * @param string $plate 车牌号
     * @param int $keepStart 保留前几位
     * @param int $keepEnd 保留后几位
     * @return string 脱敏后的车牌号
     */
    public static function maskCarPlate(string $plate, int $keepStart = 2, int $keepEnd = 2): string
    {
        $length = strlen($plate);
        if ($length <= $keepStart + $keepEnd) {
            return $plate;
        }
        return self::mask($plate, $keepStart, $length - $keepStart - $keepEnd);
    }

    /**
     * 姓名脱敏
     * @param string $name 姓名
     * @param int $keepStart 保留前几位
     * @param string $mask 脱敏字符
     * @return string 脱敏后的姓名
     */
    public static function maskName(string $name, int $keepStart = 1, string $mask = '*'): string
    {
        $length = strlen($name);
        if ($length <= $keepStart) {
            return $name;
        }
        return self::mask($name, $keepStart, $length - $keepStart, $mask);
    }

    /**
     * 字符串转支付宝账号
     * @param string $str 字符串
     * @return string 支付宝账号
     */
    public static function toAlipay(string $str): string
    {
        $str = trim($str);
        if (filter_var($str, FILTER_VALIDATE_EMAIL) || preg_match('/^1[3-9]\d{9}$/', $str)) {
            return $str;
        }
        return '';
    }

    /**
     * 字符串转银行卡号（带空格）
     * @param string $str 银行卡号
     * @return string 带空格的银行卡号
     */
    public static function toBankCardWithSpace(string $str): string
    {
        $str = self::toBankCard($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\d{4})(?=\d)/', '$1 ', $str);
    }

    /**
     * 字符串转身份证号（带空格）
     * @param string $str 身份证号
     * @return string 带空格的身份证号
     */
    public static function toIdCardWithSpace(string $str): string
    {
        $str = self::toIdCard($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\d{6})(\d{8})(\d{3})([0-9Xx])/', '$1 $2 $3$4', $str);
    }

    /**
     * 字符串转手机号（带空格）
     * @param string $str 手机号
     * @return string 带空格的手机号
     */
    public static function toPhoneWithSpace(string $str): string
    {
        $str = self::toPhone($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1 $2 $3', $str);
    }

    /**
     * 字符串转车牌号（带空格）
     * @param string $str 车牌号
     * @return string 带空格的车牌号
     */
    public static function toCarPlateWithSpace(string $str): string
    {
        $str = self::toCarPlate($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/([京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1})([A-Z]{1})([A-Z0-9]{4})([A-Z0-9挂学警港澳]{1})/', '$1 $2 $3 $4', $str);
    }

    /**
     * 字符串转邮编（带空格）
     * @param string $str 邮编
     * @return string 带空格的邮编
     */
    public static function toPostCodeWithSpace(string $str): string
    {
        $str = self::toPostCode($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\d{3})(\d{3})/', '$1 $2', $str);
    }

    /**
     * 字符串转QQ号（带空格）
     * @param string $str QQ号
     * @return string 带空格的QQ号
     */
    public static function toQqWithSpace(string $str): string
    {
        $str = self::toQq($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\d{4})(?=\d)/', '$1 ', $str);
    }

    /**
     * 字符串转微信号（带空格）
     * @param string $str 微信号
     * @return string 带空格的微信号
     */
    public static function toWechatWithSpace(string $str): string
    {
        $str = self::toWechat($str);
        if (empty($str)) {
            return '';
        }
        return preg_replace('/(\w{4})(?=\w)/', '$1 ', $str);
    }

    /**
     * 字符串转支付宝账号（带空格）
     * @param string $str 支付宝账号
     * @return string 带空格的支付宝账号
     */
    public static function toAlipayWithSpace(string $str): string
    {
        $str = self::toAlipay($str);
        if (empty($str)) {
            return '';
        }
        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return preg_replace('/(\w{4})(?=\w)/', '$1 ', $str);
        }
        return preg_replace('/(\d{3})(\d{4})(\d{4})/', '$1 $2 $3', $str);
    }

    /**
     * SQL注入防护
     * @param string $str 字符串
     * @param bool $strict 是否严格模式
     * @return string 处理后的字符串
     */
    public static function sqlSafe(string $str, bool $strict = true): string
    {
        $str = addslashes($str);
        if ($strict) {
            $str = preg_replace('/(union|select|insert|update|delete|drop|alter|truncate)/i', '', $str);
        }
        return $str;
    }

    /**
     * XSS防护
     * @param string $str 字符串
     * @param bool $strict 是否严格模式
     * @return string 处理后的字符串
     */
    public static function xssSafe(string $str, bool $strict = true): string
    {
        $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        if ($strict) {
            $str = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $str);
            $str = preg_replace('/<iframe[^>]*>.*?<\/iframe>/si', '', $str);
            $str = preg_replace('/<img[^>]*src\s*=\s*[\'\"]?([^\'\"\s>]+)[\'\"]?[^>]*>/i', '', $str);
        }
        return $str;
    }

    /**
     * HTML转义
     * @param string $str 字符串
     * @return string 转义后的字符串
     */
    public static function htmlEscape(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * HTML反转义
     * @param string $str 字符串
     * @return string 反转义后的字符串
     */
    public static function htmlUnescape(string $str): string
    {
        return htmlspecialchars_decode($str, ENT_QUOTES);
    }
}
