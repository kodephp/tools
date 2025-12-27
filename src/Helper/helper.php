<?php

use Kode\Array\Arr;
use Kode\String\Str;
use Kode\Time\Time;
use Kode\Crypto\Crypto;
use Kode\Geo\Geo;
use Kode\Ip\Ip;
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

if (!function_exists('crypto_decrypt')) {
    /**
     * 解密
     * @param string $str 字符串
     * @param string $key 密钥
     * @return string 解密后的字符串
     */
    function crypto_decrypt(string $str, string $key): string
    {
        return Crypto::decrypt($str, $key);
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
