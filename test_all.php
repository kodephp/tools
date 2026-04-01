<?php

require_once __DIR__ . '/vendor/autoload.php';

use Kode\Message\Message;
use Kode\Crypto\Crypto;
use Kode\Array\Arr;
use Kode\String\Str;
use Kode\Time\Time;
use Kode\Geo\Geo;
use Kode\Ip\Ip;

echo "=== 开始测试所有功能 ===\n\n";

// 测试Message模块
echo "1. Message模块测试\n";
$result1 = Message::result();
echo "默认结果: " . json_encode($result1) . "\n";

$result2 = Message::code(20001)->msg('请求数据有误')->result();
echo "自定义状态码和消息: " . json_encode($result2) . "\n";

$result3 = Message::data(['id' => 1])->result();
echo "带数据: " . json_encode($result3) . "\n";

$result4 = Message::data(['id' => 1])->page(1)->name('张三')->result();
echo "动态字段: " . json_encode($result4) . "\n\n";

// 测试Crypto模块
echo "2. Crypto模块测试\n";
$crypto = new Crypto('mykey1234567890ab');

// 测试特殊字符加密
$specialChars = [
    '中文' => '你好世界',
    'Emoji' => '😀🎉🚀',
    'HTML' => '<script>alert("xss")</script>',
    'JSON' => '{"key":"value"}',
    'SQL注入' => "'; DROP TABLE users; --",
];

foreach ($specialChars as $type => $str) {
    $encrypted = $crypto->encrypt($str);
    $decrypted = $crypto->decrypt($encrypted);
    echo "{$type}加密解密: " . ($str === $decrypted ? '✓' : '✗') . "\n";
}

// URL安全模式测试
$cryptoUrl = new Crypto('your_secret_key_16chars');
$cryptoUrl->mode(Crypto::MODE_URL_SAFE);
$original = '{"user_id":123,"token":"jwt..."}';
$encryptedUrl = $cryptoUrl->encrypt($original);
$decryptedUrl = $cryptoUrl->decrypt($encryptedUrl);
echo "URL安全模式: " . ($original === $decryptedUrl ? '✓' : '✗') . "\n";

// 哈希测试
$md5 = Crypto::md5('123456', 'salt');
echo "MD5哈希: " . $md5 . "\n";

$hash = Crypto::passwordHash('123456');
$verify = Crypto::passwordVerify('123456', $hash);
echo "密码哈希验证: " . ($verify ? '✓' : '✗') . "\n";

$uuid = Crypto::uuid();
echo "UUID生成: " . $uuid . "\n\n";

// 测试Array模块
echo "3. Array模块测试\n";
$list = [
    ['id' => 1, 'parent_id' => 0, 'name' => 'A'],
    ['id' => 2, 'parent_id' => 1, 'name' => 'B'],
    ['id' => 3, 'parent_id' => 1, 'name' => 'C'],
];

$tree = Arr::tree($list, 'id', 'parent_id');
echo "数组转树形: " . json_encode($tree) . "\n";

$flat = Arr::list($tree);
echo "树形转数组: " . json_encode($flat) . "\n";

$first = Arr::first([1, 2, 3]);
echo "first: " . $first . "\n";

$last = Arr::last([1, 2, 3]);
echo "last: " . $last . "\n";

$found = Arr::find([1, 2, 3], fn($n) => $n > 1);
echo "find: " . $found . "\n";

$deepMerged = Arr::deepMerge(['a' => 1], ['a' => 2, 'b' => 3]);
echo "deepMerge: " . json_encode($deepMerged) . "\n\n";

// 测试String模块
echo "4. String模块测试\n";

// 脱敏测试
$phone = Str::maskPhone('13800138000');
echo "手机号脱敏: " . $phone . "\n";

$email = Str::maskEmail('user@example.com');
echo "邮箱脱敏: " . $email . "\n";

$idCard = Str::maskIdCard('110101199001011234');
echo "身份证脱敏: " . $idCard . "\n";

// 命名转换
$camel = Str::camel('hello_world');
echo "camel: " . $camel . "\n";

$snake = Str::snake('helloWorld');
echo "snake: " . $snake . "\n";

// 编码转换
$base64 = Str::toBase64('hello');
echo "toBase64: " . $base64 . "\n";

$fromBase64 = Str::fromBase64($base64);
echo "fromBase64: " . $fromBase64 . "\n";

// XSS防护
$xss = '<script>alert("XSS")</script>';
$safe = Str::xssSafe($xss);
echo "XSS防护: " . $safe . "\n";

// SQL注入防护
$sql = "'; DROP TABLE users; --";
$safeSql = Str::sqlSafe($sql);
echo "SQL注入防护: " . $safeSql . "\n\n";

// 测试Time模块
echo "5. Time模块测试\n";

echo "当前时间: " . Time::now() . "\n";
echo "今天: " . Time::today() . "\n";
echo "昨天: " . Time::yesterday() . "\n";
echo "明天: " . Time::tomorrow() . "\n";

$timeAgo = Time::diffForHumans('2024-01-01');
echo "2024-01-01: " . $timeAgo . "\n";

echo "本周开始: " . date('Y-m-d H:i:s', Time::weekStart()) . "\n";
echo "本周结束: " . date('Y-m-d H:i:s', Time::weekEnd()) . "\n";
echo "本月开始: " . date('Y-m-d H:i:s', Time::monthStart()) . "\n";
echo "本月结束: " . date('Y-m-d H:i:s', Time::monthEnd()) . "\n\n";

// 测试Geo模块
echo "6. Geo模块测试\n";

$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737);
echo "北京到上海距离(公里): " . $distance . "\n";

$distanceM = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'm');
echo "北京到上海距离(米): " . $distanceM . "\n";

$valid = Geo::isValid(39.9042, 116.4074);
echo "坐标验证: " . ($valid ? '✓' : '✗') . "\n\n";

// 测试Ip模块
echo "7. Ip模块测试\n";

echo "客户端IP: " . Ip::get() . "\n";
echo "真实IP: " . Ip::getRealIp() . "\n";

$validIp = Ip::isValid('192.168.1.1');
echo "IP验证: " . ($validIp ? '✓' : '✗') . "\n";

$private = Ip::isPrivate('192.168.1.1');
echo "私有IP: " . ($private ? '✓' : '✗') . "\n";

$public = Ip::isPublic('8.8.8.8');
echo "公网IP: " . ($public ? '✓' : '✗') . "\n\n";

// 测试全局辅助函数
echo "8. 全局辅助函数测试\n";

echo "arr_first: " . arr_first([1, 2, 3]) . "\n";
echo "arr_last: " . arr_last([1, 2, 3]) . "\n";
echo "str_mask_phone: " . str_mask_phone('13800138000') . "\n";
echo "str_to_base64: " . str_to_base64('hello') . "\n";
echo "time_now: " . time_now() . "\n";
echo "uuid: " . str_uuid() . "\n\n";

echo "=== 所有测试完成 ===\n";
