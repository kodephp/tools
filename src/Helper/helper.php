<?php

declare(strict_types=1);

use Kode\Array\Arr;
use Kode\String\Str;
use Kode\Time\Time;
use Kode\Crypto\Crypto;
use Kode\Geo\Geo;
use Kode\Ip\Ip;
use Kode\Math\Math;
use Kode\Security\Security;
use Kode\Curl\Curl;
use Kode\Curl\Response;
use Kode\Qrcode\Qr;
use Endroid\QrCode\Color\Color as QrColor;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Label;

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
        $length = max(1, $length);
        $charLength = strlen($chars);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charLength - 1)];
        }
        return $result;
    }
}

if (!function_exists('str_uuid')) {
    /**
     * 生成UUID
     * @param string|null $format 自定义格式
     * @return string UUID
     */
    function str_uuid(?string $format = null): string
    {
        return Str::uuid($format);
    }
}

if (!function_exists('str_uuid_batch')) {
    /**
     * 批量生成唯一字符串
     * @param int $count 生成数量
     * @param string|null $format 自定义格式
     * @return list<string|int> 唯一字符串数组
     */
    function str_uuid_batch(int $count, ?string $format = null): array
    {
        return Str::uuidBatch($count, $format);
    }
}

if (!function_exists('str_ordered_uuid')) {
    /**
     * 生成按时间排序的唯一 ID
     * @param string|null $format 自定义格式
     * @return string 唯一 ID
     */
    function str_ordered_uuid(?string $format = null): string
    {
        return Str::orderedUuid($format);
    }
}

if (!function_exists('str_code')) {
    /**
     * 按自定义格式生成随机码
     * @param string $format 格式字符串
     * @return string 随机码
     */
    function str_code(string $format): string
    {
        return Str::code($format);
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
        return (new Crypto($key))->encrypt($str);
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
        return (new Crypto($key))->decrypt($str);
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

if (!function_exists('str_mask_keep')) {
    /**
     * Unicode 感知脱敏（保留头部/尾部指定字符数）
     * @param string $str 原字符串
     * @param int $head 保留前几位
     * @param int $tail 保留后几位
     * @param string $mask 掩码字符
     * @return string 脱敏后的字符串
     */
    function str_mask_keep(string $str, int $head = 0, int $tail = 0, string $mask = '*'): string
    {
        return Str::maskKeep($str, $head, $tail, $mask);
    }
}

if (!function_exists('str_validate_plate')) {
    /**
     * 验证车牌号
     * @param string $plate 车牌号
     * @param string $type 验证类型
     * @param array $whitelist 白名单
     * @return bool 是否有效
     */
    function str_validate_plate(string $plate, string $type = Str::PLATE_ALL, array $whitelist = []): bool
    {
        return Str::validatePlate($plate, $type, $whitelist);
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
     * 计算两个坐标之间的距离
     * @param float $lat1 第一个点的纬度
     * @param float $lon1 第一个点的经度
     * @param float $lat2 第二个点的纬度
     * @param float $lon2 第二个点的经度
     * @param string $unit 单位（km:公里, mi:英里, m:米）
     * @return float 两点之间的距离
     */
    function geo_distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'km'): float
    {
        return Geo::distance($lat1, $lon1, $lat2, $lon2, $unit);
    }
}

if (!function_exists('ip_get_real')) {
    /**
     * 获取真实客户端IP地址
     * @return string|null 真实IP地址，如果未找到则返回null
     */
    function ip_get_real(): ?string
    {
        return Ip::getRealIp();
    }
}

if (!function_exists('ip_is_valid')) {
    /**
     * 验证IP地址格式
     * @param string $ip 要验证的IP地址
     * @return bool 如果IP地址有效则返回true
     */
    function ip_is_valid(string $ip): bool
    {
        return Ip::isValid($ip);
    }
}

if (!function_exists('ip_is_private')) {
    /**
     * 检查IP地址是否为私有/内部地址
     * @param string $ip 要检查的IP地址
     * @return bool 如果是私有IP则返回true
     */
    function ip_is_private(string $ip): bool
    {
        return Ip::isPrivate($ip);
    }
}

if (!function_exists('ip_in_cidr')) {
    /**
     * 检查IP是否属于指定CIDR网段
     * @param string $ip IP地址
     * @param string $cidr CIDR网段
     * @return bool 是否属于该网段
     */
    function ip_in_cidr(string $ip, string $cidr): bool
    {
        return Ip::inCidr($ip, $cidr);
    }
}

if (!function_exists('ip_in_range')) {
    /**
     * 检查IP是否在指定IP范围内
     * @param string $ip IP地址
     * @param string $range IP范围
     * @return bool 是否在范围内
     */
    function ip_in_range(string $ip, string $range): bool
    {
        return Ip::inRange($ip, $range);
    }
}

if (!function_exists('arr_first')) {
    /**
     * 获取数组第一个元素
     * @param array $array 数组
     * @return mixed 第一个元素
     */
    function arr_first(array $array): mixed
    {
        return Arr::first($array);
    }
}

if (!function_exists('arr_last')) {
    /**
     * 获取数组最后一个元素
     * @param array $array 数组
     * @return mixed 最后一个元素
     */
    function arr_last(array $array): mixed
    {
        return Arr::last($array);
    }
}

if (!function_exists('arr_find')) {
    /**
     * 数组查找
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return mixed|null 查找结果
     */
    function arr_find(array $array, callable $callback): mixed
    {
        return Arr::find($array, $callback);
    }
}

if (!function_exists('arr_find_key')) {
    /**
     * 数组查找键名
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return string|int|null 键名
     */
    function arr_find_key(array $array, callable $callback): string|int|null
    {
        return Arr::findKey($array, $callback);
    }
}

if (!function_exists('arr_any')) {
    /**
     * 数组是否存在满足条件的元素
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否存在
     */
    function arr_any(array $array, callable $callback): bool
    {
        return Arr::any($array, $callback);
    }
}

if (!function_exists('arr_all')) {
    /**
     * 数组是否所有元素都满足条件
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否都满足
     */
    function arr_all(array $array, callable $callback): bool
    {
        return Arr::all($array, $callback);
    }
}

if (!function_exists('arr_pluck')) {
    /**
     * 数组列提取
     * @param array $array 数组
     * @param string $columnKey 列键名
     * @param string|null $indexKey 索引键名
     * @return array 提取后的数组
     */
    function arr_pluck(array $array, string $columnKey, ?string $indexKey = null): array
    {
        return Arr::pluck($array, $columnKey, $indexKey);
    }
}

if (!function_exists('arr_to_tree')) {
    /**
     * 数组转树形结构
     * @param array $list 数组
     * @param string $idField ID字段名
     * @param string $parentIdField 父ID字段名
     * @param string $childrenField 子节点字段名
     * @return array 树形结构
     */
    function arr_to_tree(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $childrenField = 'children'): array
    {
        return Arr::toTree($list, $idField, $parentIdField, $childrenField);
    }
}

if (!function_exists('arr_from_tree')) {
    /**
     * 树形结构转数组
     * @param array $tree 树形结构
     * @param string $childrenField 子节点字段名
     * @return array 数组
     */
    function arr_from_tree(array $tree, string $childrenField = 'children'): array
    {
        return Arr::fromTree($tree, $childrenField);
    }
}

if (!function_exists('str_truncate')) {
    /**
     * 字符串截断
     * @param string $str 字符串
     * @param int $length 截断长度
     * @param string $suffix 后缀
     * @return string 截断后的字符串
     */
    function str_truncate(string $str, int $length, string $suffix = '...'): string
    {
        return Str::truncate($str, $length, $suffix);
    }
}

if (!function_exists('str_limit')) {
    /**
     * 字符串限制
     * @param string $str 字符串
     * @param int $limit 限制长度
     * @param string $suffix 后缀
     * @return string 限制后的字符串
     */
    function str_limit(string $str, int $limit, string $suffix = '...'): string
    {
        return Str::limit($str, $limit, $suffix);
    }
}

if (!function_exists('str_limit_length')) {
    /**
     * 字符串限制长度
     * @param string $str 字符串
     * @param int $limit 限制长度
     * @param string $suffix 后缀
     * @return string 限制后的字符串
     */
    function str_limit_length(string $str, int $limit, string $suffix = '...'): string
    {
        return Str::limitLength($str, $limit, $suffix);
    }
}

if (!function_exists('str_mb_strcut')) {
    /**
     * 多字节字符串截断（按字节长度）
     * @param string $str 字符串
     * @param int $start 开始位置
     * @param int|null $length 截断字节长度
     * @param string $encoding 编码
     * @return string 截断后的字符串
     */
    function str_mb_strcut(string $str, int $start, ?int $length = null, string $encoding = 'UTF-8'): string
    {
        return Str::mbStrcut($str, $start, $length, $encoding);
    }
}

if (!function_exists('str_snake')) {
    /**
     * 驼峰转下划线
     * @param string $str 字符串
     * @param string $separator 分隔符
     * @return string 下划线命名
     */
    function str_snake(string $str, string $separator = '_'): string
    {
        return Str::snake($str, $separator);
    }
}

if (!function_exists('str_contains')) {
    /**
     * 字符串是否包含
     * @param string $str 字符串
     * @param string $needle 查找字符串
     * @return bool 是否包含
     */
    function str_contains(string $str, string $needle): bool
    {
        return Str::contains($str, $needle);
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * 字符串是否以开头
     * @param string $str 字符串
     * @param string $prefix 前缀
     * @return bool 是否以开头
     */
    function str_starts_with(string $str, string $prefix): bool
    {
        return Str::startsWith($str, $prefix);
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * 字符串是否以结尾
     * @param string $str 字符串
     * @param string $suffix 后缀
     * @return bool 是否以结尾
     */
    function str_ends_with(string $str, string $suffix): bool
    {
        return Str::endsWith($str, $suffix);
    }
}

if (!function_exists('str_replace_array')) {
    /**
     * 字符串替换多个
     * @param string $str 字符串
     * @param array $replace 替换数组
     * @return string 替换后的字符串
     */
    function str_replace_array(string $str, array $replace): string
    {
        return Str::replaceArray($str, $replace);
    }
}

if (!function_exists('str_remove')) {
    /**
     * 字符串删除
     * @param string $str 字符串
     * @param string $search 删除字符串
     * @return string 删除后的字符串
     */
    function str_remove(string $str, string $search): string
    {
        return Str::remove($str, $search);
    }
}

if (!function_exists('str_remove_array')) {
    /**
     * 字符串删除多个
     * @param string $str 字符串
     * @param array $search 删除字符串数组
     * @return string 删除后的字符串
     */
    function str_remove_array(string $str, array $search): string
    {
        return Str::removeArray($str, $search);
    }
}

if (!function_exists('str_join')) {
    /**
     * 字符串连接
     * @param array $array 数组
     * @param string $separator 分隔符
     * @return string 连接后的字符串
     */
    function str_join(array $array, string $separator = ''): string
    {
        return Str::join($array, $separator);
    }
}

if (!function_exists('str_unique')) {
    /**
     * 字符串去重
     * @param string $str 字符串
     * @return string 去重后的字符串
     */
    function str_unique(string $str): string
    {
        return Str::unique($str);
    }
}

if (!function_exists('str_shuffle')) {
    /**
     * 字符串打乱
     * @param string $str 字符串
     * @return string 打乱后的字符串
     */
    function str_shuffle(string $str): string
    {
        return Str::shuffle($str);
    }
}

if (!function_exists('str_substr')) {
    /**
     * 字符串截取
     * @param string $str 字符串
     * @param int $start 开始位置
     * @param int|null $length 截取长度
     * @return string 截取后的字符串
     */
    function str_substr(string $str, int $start, ?int $length = null): string
    {
        return Str::substr($str, $start, $length);
    }
}

if (!function_exists('str_mb_substr')) {
    /**
     * 字符串截取多字节
     * @param string $str 字符串
     * @param int $start 开始位置
     * @param int|null $length 截取长度
     * @return string 截取后的字符串
     */
    function str_mb_substr(string $str, int $start, ?int $length = null): string
    {
        return Str::mbSubstr($str, $start, $length);
    }
}

if (!function_exists('str_length')) {
    /**
     * 字符串长度
     * @param string $str 字符串
     * @return int 长度
     */
    function str_length(string $str): int
    {
        return Str::length($str);
    }
}

if (!function_exists('str_mb_length')) {
    /**
     * 字符串多字节长度
     * @param string $str 字符串
     * @return int 长度
     */
    function str_mb_length(string $str): int
    {
        return Str::mbLength($str);
    }
}

if (!function_exists('str_to_binary')) {
    /**
     * 字符串转二进制
     * @param string $str 字符串
     * @return string 二进制字符串
     */
    function str_to_binary(string $str): string
    {
        return Str::toBinary($str);
    }
}

if (!function_exists('str_from_binary')) {
    /**
     * 二进制转字符串
     * @param string $binary 二进制字符串
     * @return string 字符串
     */
    function str_from_binary(string $binary): string
    {
        return Str::fromBinary($binary);
    }
}

if (!function_exists('str_to_hex')) {
    /**
     * 字符串转十六进制
     * @param string $str 字符串
     * @return string 十六进制字符串
     */
    function str_to_hex(string $str): string
    {
        return Str::toHex($str);
    }
}

if (!function_exists('str_from_hex')) {
    /**
     * 十六进制转字符串
     * @param string $hex 十六进制字符串
     * @return string 字符串
     */
    function str_from_hex(string $hex): string
    {
        return Str::fromHex($hex);
    }
}

if (!function_exists('str_to_base64')) {
    /**
     * 字符串转Base64
     * @param string $str 字符串
     * @return string Base64字符串
     */
    function str_to_base64(string $str): string
    {
        return Str::toBase64($str);
    }
}

if (!function_exists('str_from_base64')) {
    /**
     * Base64转字符串
     * @param string $base64 Base64字符串
     * @return string 字符串
     */
    function str_from_base64(string $base64): string
    {
        return Str::fromBase64($base64);
    }
}

if (!function_exists('str_to_url_encode')) {
    /**
     * 字符串转URL编码
     * @param string $str 字符串
     * @return string URL编码字符串
     */
    function str_to_url_encode(string $str): string
    {
        return Str::toUrlEncode($str);
    }
}

if (!function_exists('str_from_url_decode')) {
    /**
     * URL编码转字符串
     * @param string $urlEncoded URL编码字符串
     * @return string 字符串
     */
    function str_from_url_decode(string $urlEncoded): string
    {
        return Str::fromUrlDecode($urlEncoded);
    }
}

if (!function_exists('str_compress')) {
    /**
     * 字符串压缩
     * @param string $str 字符串
     * @param int $level 压缩级别（0-9）
     * @return string 压缩后的字符串
     */
    function str_compress(string $str, int $level = -1): string
    {
        return Str::compress($str, $level);
    }
}

if (!function_exists('str_decompress')) {
    /**
     * 字符串解压
     * @param string $compressed 压缩后的字符串
     * @return string 解压后的字符串
     */
    function str_decompress(string $compressed): string
    {
        return Str::decompress($compressed);
    }
}

if (!function_exists('curl_get')) {
    /**
     * 发送GET请求
     * @param string $url 请求URL
     * @param array $query 查询参数
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_get(string $url, array $query = [], array $options = []): Response
    {
        return Curl::get($url, $query)->curlOptions($options)->send();
    }
}

if (!function_exists('curl_post')) {
    /**
     * 发送POST请求
     * @param string $url 请求URL
     * @param mixed $data 请求数据
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_post(string $url, mixed $data = [], array $options = []): Response
    {
        return Curl::post($url, $data)->curlOptions($options)->send();
    }
}

if (!function_exists('curl_put')) {
    /**
     * 发送PUT请求
     * @param string $url 请求URL
     * @param mixed $data 请求数据
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_put(string $url, mixed $data = [], array $options = []): Response
    {
        return Curl::put($url, $data)->curlOptions($options)->send();
    }
}

if (!function_exists('curl_patch')) {
    /**
     * 发送PATCH请求
     * @param string $url 请求URL
     * @param mixed $data 请求数据
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_patch(string $url, mixed $data = [], array $options = []): Response
    {
        return Curl::patch($url, $data)->curlOptions($options)->send();
    }
}

if (!function_exists('curl_delete')) {
    /**
     * 发送DELETE请求
     * @param string $url 请求URL
     * @param array $query 查询参数
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_delete(string $url, array $query = [], array $options = []): Response
    {
        return Curl::delete($url)->query($query)->curlOptions($options)->send();
    }
}

if (!function_exists('curl_request')) {
    /**
     * 发送HTTP请求
     * @param string $method HTTP方法
     * @param string $url 请求URL
     * @param mixed $data 请求数据
     * @param array $options 请求选项
     * @return Response 响应对象
     */
    function curl_request(string $method, string $url, mixed $data = null, array $options = []): Response
    {
        $curl = (new Curl($url))->method($method);
        if ($data !== null) {
            $curl->body($data);
        }
        return $curl->curlOptions($options)->send();
    }
}

if (!function_exists('curl_pool')) {
    /**
     * 并发请求
     * @param array $requests 请求配置数组
     * @return array 响应对象数组
     */
    function curl_pool(array $requests): array
    {
        return Curl::pool($requests);
    }
}

if (!function_exists('qr_create')) {
    /**
     * 创建二维码
     * @param string $text 二维码内容
     * @param int $size 大小
     * @param int|null $margin 边距
     * @return Qr 二维码实例
     */
    function qr_create(string $text, int $size = 300, ?int $margin = null): Qr
    {
        $qr = Qr::create($text)->size($size);
        if ($margin !== null) {
            $qr->margin($margin);
        }
        return $qr;
    }
}

if (!function_exists('qr_text')) {
    /**
     * 创建文本二维码
     * @param string $text 文本内容
     * @param int $size 大小
     * @param int|null $margin 边距
     * @param string $foreground 前景色hex
     * @param string $background 背景色hex
     * @return Qr 二维码实例
     */
    function qr_text(string $text, int $size = 300, ?int $margin = null, string $foreground = '#000000', string $background = '#FFFFFF'): Qr
    {
        $qr = Qr::create($text)->size($size);
        if ($margin !== null) {
            $qr->margin($margin);
        }
        $fgRgb = qr_hex_to_rgb($foreground);
        $bgRgb = qr_hex_to_rgb($background);
        $qr->foregroundColor($fgRgb[0], $fgRgb[1], $fgRgb[2])
           ->backgroundColor($bgRgb[0], $bgRgb[1], $bgRgb[2]);
        return $qr;
    }
}

if (!function_exists('qr_url')) {
    /**
     * 创建URL二维码
     * @param string $url URL地址
     * @param int $size 大小
     * @param int|null $margin 边距
     * @return Qr 二维码实例
     */
    function qr_url(string $url, int $size = 300, ?int $margin = null): Qr
    {
        $qr = Qr::create($url)->size($size);
        if ($margin !== null) {
            $qr->margin($margin);
        }
        return $qr;
    }
}

if (!function_exists('qr_wifi')) {
    /**
     * 创建WiFi二维码
     * @param string $ssid WiFi名称
     * @param string $password WiFi密码
     * @param string $encryption 加密方式(wpa, wep, nopass)
     * @param bool $hidden 是否隐藏网络
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_wifi(string $ssid, string $password, string $encryption = 'wpa', bool $hidden = false, int $size = 300): Qr
    {
        return Qr::wifi($ssid, $password, $encryption, $hidden)->size($size);
    }
}

if (!function_exists('qr_email')) {
    /**
     * 创建邮件二维码
     * @param string $email 邮箱地址
     * @param string|null $subject 主题
     * @param string|null $body 内容
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_email(string $email, ?string $subject = null, ?string $body = null, int $size = 300): Qr
    {
        $qr = Qr::email($email, $subject, $body);
        if ($size !== 300) {
            $qr->size($size);
        }
        return $qr;
    }
}

if (!function_exists('qr_phone')) {
    /**
     * 创建电话二维码
     * @param string $phone 电话号码
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_phone(string $phone, int $size = 300): Qr
    {
        return Qr::phone($phone)->size($size);
    }
}

if (!function_exists('qr_sms')) {
    /**
     * 创建短信二维码
     * @param string $phone 电话号码
     * @param string|null $body 短信内容
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_sms(string $phone, ?string $body = null, int $size = 300): Qr
    {
        $qr = Qr::sms($phone, $body);
        if ($size !== 300) {
            $qr->size($size);
        }
        return $qr;
    }
}

if (!function_exists('qr_vcard')) {
    /**
     * 创建名片二维码
     * @param array $info 联系人信息 (firstName, lastName, phone, email, org, title, url, address)
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_vcard(array $info, int $size = 300): Qr
    {
        $firstName = $info['firstName'] ?? '';
        $lastName = $info['lastName'] ?? '';
        return Qr::vcard($info, $firstName, $lastName)->size($size);
    }
}

if (!function_exists('qr_geo')) {
    /**
     * 创建位置二维码
     * @param float $lat 纬度
     * @param float $lon 经度
     * @param int $size 大小
     * @return Qr 二维码实例
     */
    function qr_geo(float $lat, float $lon, int $size = 300): Qr
    {
        return Qr::geo($lat, $lon)->size($size);
    }
}

if (!function_exists('qr_color')) {
    /**
     * 创建颜色实例
     * @param int $red 红色
     * @param int $green 绿色
     * @param int $blue 蓝色
     * @return QrColor 颜色实例
     */
    function qr_color(int $red, int $green, int $blue): QrColor
    {
        return new QrColor($red, $green, $blue);
    }
}

if (!function_exists('qr_hex_to_rgb')) {
    /**
     * HEX颜色转RGB
     * @param string $hex HEX颜色值
     * @return array RGB数组
     */
    function qr_hex_to_rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}

if (!function_exists('math_set_default_scale')) {
    /**
     * 设置 Math 全局默认保留小数位数
     * @param int $scale 小数位数
     * @return void
     */
    function math_set_default_scale(int $scale): void
    {
        Math::setDefaultScale($scale);
    }
}

if (!function_exists('math_get_default_scale')) {
    /**
     * 获取 Math 全局默认保留小数位数
     * @return int 小数位数
     */
    function math_get_default_scale(): int
    {
        return Math::getDefaultScale();
    }
}

if (!function_exists('math_add')) {
    /**
     * 高精度加法
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int|null $scale 保留小数位数
     * @return string 结果
     */
    function math_add(float|int|string $num1, float|int|string $num2, ?int $scale = null): string
    {
        return Math::add($num1, $num2, $scale);
    }
}

if (!function_exists('math_sub')) {
    /**
     * 高精度减法
     * @param float|int|string $num1 被减数
     * @param float|int|string $num2 减数
     * @param int|null $scale 保留小数位数
     * @return string 结果
     */
    function math_sub(float|int|string $num1, float|int|string $num2, ?int $scale = null): string
    {
        return Math::sub($num1, $num2, $scale);
    }
}

if (!function_exists('math_mul')) {
    /**
     * 高精度乘法
     * @param float|int|string $num1 第一个数
     * @param float|int|string $num2 第二个数
     * @param int|null $scale 保留小数位数
     * @return string 结果
     */
    function math_mul(float|int|string $num1, float|int|string $num2, ?int $scale = null): string
    {
        return Math::mul($num1, $num2, $scale);
    }
}

if (!function_exists('math_div')) {
    /**
     * 高精度除法
     * @param float|int|string $num1 被除数
     * @param float|int|string $num2 除数
     * @param int|null $scale 保留小数位数
     * @return string 结果
     */
    function math_div(float|int|string $num1, float|int|string $num2, ?int $scale = null): string
    {
        return Math::div($num1, $num2, $scale);
    }
}

if (!function_exists('math_discount')) {
    /**
     * 计算折扣价
     * @param float|int|string $price 原价
     * @param float|int|string $discount 折扣
     * @param int $scale 保留小数位数
     * @return string 折后价格
     */
    function math_discount(float|int|string $price, float|int|string $discount, int $scale = 2): string
    {
        return Math::discount($price, $discount, $scale);
    }
}

if (!function_exists('math_tax')) {
    /**
     * 计算税额
     * @param float|int|string $amount 金额
     * @param float|int|string $rate 税率
     * @param int $scale 保留小数位数
     * @return string 税费
     */
    function math_tax(float|int|string $amount, float|int|string $rate, int $scale = 2): string
    {
        return Math::tax($amount, $rate, $scale);
    }
}

if (!function_exists('math_average')) {
    /**
     * 计算平均值
     * @param array $numbers 数值数组
     * @param int|null $scale 保留小数位数
     * @return string 平均值
     */
    function math_average(array $numbers, ?int $scale = null): string
    {
        return Math::average($numbers, $scale);
    }
}

if (!function_exists('math_median')) {
    /**
     * 计算中位数
     * @param array $numbers 数值数组
     * @param int|null $scale 保留小数位数
     * @return string 中位数
     */
    function math_median(array $numbers, ?int $scale = null): string
    {
        return Math::median($numbers, $scale);
    }
}

if (!function_exists('security_rate_limit')) {
    /**
     * 检查是否超过限速
     * @param string $key 限速标识
     * @param int $maxAttempts 最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return bool true = 允许通过
     */
    function security_rate_limit(string $key, int $maxAttempts = 60, int $windowSeconds = 60): bool
    {
        return Security::rateLimit($key, $maxAttempts, $windowSeconds);
    }
}

if (!function_exists('security_rate_limit_remaining')) {
    /**
     * 获取剩余可用请求次数
     * @param string $key 限速标识
     * @param int $maxAttempts 最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return int 剩余次数
     */
    function security_rate_limit_remaining(string $key, int $maxAttempts = 60, int $windowSeconds = 60): int
    {
        return Security::rateLimitRemaining($key, $maxAttempts, $windowSeconds);
    }
}

if (!function_exists('security_csrf_token')) {
    /**
     * 生成 CSRF Token
     * @param string|null $sessionKey 会话键名
     * @return string Token
     */
    function security_csrf_token(?string $sessionKey = null): string
    {
        return Security::csrfToken($sessionKey);
    }
}

if (!function_exists('security_csrf_verify')) {
    /**
     * 验证 CSRF Token
     * @param string $token 待验证 Token
     * @param string|null $sessionKey 会话键名
     * @param bool $clear 是否一次性
     * @return bool 是否有效
     */
    function security_csrf_verify(string $token, ?string $sessionKey = null, bool $clear = false): bool
    {
        return Security::csrfVerify($token, $sessionKey, $clear);
    }
}

if (!function_exists('security_sign')) {
    /**
     * 生成请求签名
     * @param array $data 待签名数据
     * @param string $secret 密钥
     * @param int|null $timestamp 时间戳
     * @return string 签名
     */
    function security_sign(array $data, string $secret, ?int $timestamp = null): string
    {
        return Security::sign($data, $secret, $timestamp);
    }
}

if (!function_exists('security_sign_verify')) {
    /**
     * 验证请求签名
     * @param array $data 接收数据
     * @param string $secret 密钥
     * @param int $expire 有效期（秒）
     * @return bool 是否有效
     */
    function security_sign_verify(array $data, string $secret, int $expire = 300): bool
    {
        return Security::signVerify($data, $secret, $expire);
    }
}

if (!function_exists('security_input')) {
    /**
     * 安全获取输入值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @param string $type 目标类型
     * @param string $source 数据源
     * @return mixed 过滤后的值
     */
    function security_input(string $key, mixed $default = null, string $type = 'string', string $source = 'request'): mixed
    {
        return Security::input($key, $default, $type, $source);
    }
}

if (!function_exists('security_xss_clean')) {
    /**
     * XSS 清理
     * @param string $str 字符串
     * @return string 清理后的字符串
     */
    function security_xss_clean(string $str): string
    {
        return Security::xssClean($str);
    }
}

if (!function_exists('security_random_token')) {
    /**
     * 生成随机 Token
     * @param int $length 长度
     * @return string Token
     */
    function security_random_token(int $length = 32): string
    {
        return Security::randomToken($length);
    }
}

if (!function_exists('security_rate_limit_storage')) {
    /**
     * 设置限速存储后端
     * @param \Kode\Security\Contracts\RateLimiterStorageInterface $storage 存储实例
     * @return void
     */
    function security_rate_limit_storage(\Kode\Security\Contracts\RateLimiterStorageInterface $storage): void
    {
        Security::setRateLimiterStorage($storage);
    }
}

if (!function_exists('security_rate_limit_available')) {
    /**
     * 仅查询限速剩余次数（不增加计数）
     * @param string $key 限速标识
     * @param int $maxAttempts 最大次数
     * @param int $windowSeconds 窗口时长（秒）
     * @return int 剩余次数
     */
    function security_rate_limit_available(string $key, int $maxAttempts = 60, int $windowSeconds = 60): int
    {
        return Security::rateLimitAvailable($key, $maxAttempts, $windowSeconds);
    }
}

if (!function_exists('security_fingerprint')) {
    /**
     * 生成请求指纹
     * @param array $extra 额外参与哈希的字段
     * @return string 64 位十六进制指纹
     */
    function security_fingerprint(array $extra = []): string
    {
        return Security::requestFingerprint($extra);
    }
}

if (!function_exists('security_nonce')) {
    /**
     * 生成一次性 Nonce
     * @param string $namespace 命名空间
     * @param int $ttl 有效时间（秒）
     * @return string Nonce Token
     */
    function security_nonce(string $namespace = 'nonce', int $ttl = 300): string
    {
        return Security::nonce($namespace, $ttl);
    }
}

if (!function_exists('security_verify_nonce')) {
    /**
     * 验证并消耗一次性 Nonce
     * @param string $token 待验证的 Nonce
     * @param string $namespace 命名空间
     * @param int $ttl 有效时间（秒）
     * @return bool 是否首次有效
     */
    function security_verify_nonce(string $token, string $namespace = 'nonce', int $ttl = 300): bool
    {
        return Security::verifyNonce($token, $namespace, $ttl);
    }
}
