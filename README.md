# kode/tools - PHP8.1+ 通用工具包

<!-- TOC -->

* [简介](#简介)
* [安装](#安装)
* [消息体模块](#消息体模块)
* [加解密模块](#加解密模块)
* [HTTP请求模块](#http请求模块)
* [数组处理模块](#数组处理模块)
* [字符串处理模块](#字符串处理模块)
* [时间处理模块](#时间处理模块)
* [数学计算模块](#数学计算模块)
* [地理位置模块](#地理位置模块)
* [IP地址模块](#ip地址模块)
* [全局辅助函数](#全局辅助函数)
* [许可证](#许可证)

<!-- TOC -->

## 简介

这是一个基于PHP8.1+特性开发的模块化通用工具包，提供了消息体、数组处理、字符串处理、时间处理、加解密、IP地址处理、地理计算、HTTP请求、全局辅助方法等功能。支持对象和静态两种调用方式。

## 安装

```bash
composer require kode/tools
```

## 消息体模块

### 特性

- ✅ 灵活的链式调用（code/data/msg等方法可任意顺序调用）
- ✅ 默认200状态码，默认消息"成功"
- ✅ 支持动态添加任意字段（page/name/total等）
- ✅ 内置状态码映射（HTTP标准码+业务码）
- ✅ 支持自定义状态码映射
- ✅ XSS防护（可选）
- ✅ 危险方法名防护

### 快速开始

```php
use Kode\Message\Message;

// 默认200，msg="成功"
Message::result();
// ['code' => 200, 'msg' => '成功']

// 指定状态码和消息
Message::code(20001)->msg('请求数据有误')->result();
// ['code' => 20001, 'msg' => '请求数据有误']

// 传入data数据（无data不显示data字段）
Message::data(['id' => 1])->result();
// ['code' => 200, 'msg' => '成功', 'data' => ['id' => 1]]

// 链式调用位置不约束
Message::data(['id' => 1])->code(20001)->msg('请求数据有误')->result();
// ['code' => 20001, 'msg' => '请求数据有误', 'data' => ['id' => 1]]

// 动态添加任意字段
Message::data(['id' => 1])->page(1)->name('张三')->result();
// ['code' => 200, 'msg' => '成功', 'data' => ['id' => 1], 'page' => 1, 'name' => '张三']
```

### 内置状态码

```php
// HTTP标准码
200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
403 => 'Forbidden', 404 => 'Not Found', 500 => 'Internal Server Error'

// 业务码
300000 => 'Token无效', 300001 => 'Token已过期',
400000 => '参数错误', 500000 => '数据库错误'
```

### 自定义状态码

```php
// 合并自定义状态码
Message::codes([
    800000 => '自定义业务异常',
    900000 => '权限不足'
]);

// 从配置文件加载
Message::loadCodes('config/codes.php');
```

## 加解密模块

### 特性

- ✅ AES-256-GCM加密
- ✅ Sodium和OpenSSL双引擎自动切换
- ✅ MD5/SHA系列哈希（支持加盐）
- ✅ 密码哈希和验证
- ✅ HMAC签名
- ✅ UUID/Token/邀请码/验证码生成

### 快速开始

```php
use Kode\Crypto\Crypto;

// AES加密解密
$encrypted = (new Crypto('mykey123456789'))->encrypt('敏感数据');
$decrypted = (new Crypto('mykey123456789'))->decrypt($encrypted);

// MD5哈希（加盐）
$md5 = Crypto::md5('123456', 'salt');

// 密码哈希
$hash = Crypto::passwordHash('123456');
$verify = Crypto::passwordVerify('123456', $hash);

// UUID/Token生成
$uuid = Crypto::uuid();
$token = Crypto::token(32);

// HMAC签名
$hmac = Crypto::hmac('数据', '密钥', 'sha256');
```

## HTTP请求模块

### 特性

- ✅ GET/POST/PUT/PATCH/DELETE/HEAD/OPTIONS
- ✅ 链式调用
- ✅ JSON/表单/文件上传
- ✅ SSL验证和代理支持
- ✅ 重试机制
- ✅ 中间件支持
- ✅ Promise风格then/catch

### 快速开始

```php
use Kode\Curl\Curl;

// GET请求
$response = Curl::get('https://api.example.com/users', ['page' => 1])
    ->timeout(30)
    ->headers(['Authorization' => 'Bearer xxx'])
    ->send();

// POST请求
$response = Curl::post('https://api.example.com/users', ['name' => '张三'])
    ->withJson(['name' => '张三'])
    ->send();

// 链式调用
$response = Curl::create('https://api.example.com/users')
    ->method('POST')
    ->withJson(['name' => '张三'])
    ->authorization('Bearer xxx')
    ->send();

// Promise风格
$result = Curl::get('https://api.example.com/users')
    ->then(fn($response) => $response->json())
    ->catch(fn($response) => $response->getErrorMessage());
```

## 数组处理模块

### 特性

- ✅ 树形结构转换
- ✅ first/last/find 数组操作
- ✅ 深度合并
- ✅ 点语法访问

### 快速开始

```php
use Kode\Array\Arr;

// 数组转树形
$tree = Arr::tree([
    ['id' => 1, 'parent_id' => 0, 'name' => 'A'],
    ['id' => 2, 'parent_id' => 1, 'name' => 'B'],
], 'id', 'parent_id');

// 获取首尾元素
Arr::first([1, 2, 3]); // 1
Arr::last([1, 2, 3]);  // 3

// 查找元素
Arr::find([1, 2, 3], fn($n) => $n > 1); // 2

// 深度合并
Arr::deepMerge(['a' => 1], ['a' => 2, 'b' => 3]); // ['a' => 2, 'b' => 3]
```

## 字符串处理模块

### 特性

- ✅ 手机号/邮箱/身份证脱敏
- ✅ 驼峰/蛇形命名转换
- ✅ Base64编码解码
- ✅ UUID生成
- ✅ 字符串验证

### 快速开始

```php
use Kode\String\Str;

// 脱敏
Str::maskPhone('13800138000');     // 138****8000
Str::maskEmail('user@example.com'); // us***@example.com

// 命名转换
Str::camel('hello_world');  // helloWorld
Str::snake('helloWorld');   // hello_world

// Base64
Str::toBase64('hello');    // aGVsbG8=
Str::fromBase64('aGVsbG8='); // hello

// UUID
Str::uuid(); // 生成UUID

// 验证
Str::validatePhone('13800138000');  // true
Str::validateEmail('test@test.com'); // true
```

## 时间处理模块

### 特性

- ✅ 时间格式化
- ✅ 人性化显示（3分钟前）
- ✅ 日期范围计算
- ✅ 时间加减计算

### 快速开始

```php
use Kode\Time\Time;

// 获取当前时间
Time::now();        // 2024-01-01 12:00:00
Time::today();      // 2024-01-01

// 人性化显示
Time::diffForHumans('2024-01-01'); // 3天前

// 日期范围
Time::weekStart();  // 本周开始
Time::monthStart(); // 本月开始
```

## 数学计算模块

### 特性

- ✅ 高精度计算（bcmath）
- ✅ 折扣/税费计算
- ✅ 平均数/中位数/标准差

### 快速开始

```php
use Kode\Math\Math;

// 高精度计算
Math::add('1.1', '2.2');   // 3.3000000000
Math::sub('5.5', '3.3');   // 2.2000000000

// 折扣计算
Math::discount('100', '0.8'); // 80（8折）

// 税费计算
Math::tax('100', '0.13');   // 13（13%税）
```

## 地理位置模块

### 特性

- ✅ 两点距离计算（Haversine）
- ✅ 坐标验证
- ✅ WGS84/GCJ02/BD09转换

### 快速开始

```php
use Kode\Geo\Geo;

// 两点距离（米）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737);

// 两点距离（公里）
$distanceKm = Geo::distanceKm(39.9042, 116.4074, 31.2304, 121.4737);

// 坐标验证
Geo::isValid(39.9042, 116.4074); // true
```

## IP地址模块

### 特性

- ✅ IP验证
- ✅ 私有/公网IP判断

### 快速开始

```php
use Kode\Ip\Ip;

// IP验证
Ip::isValid('192.168.1.1'); // true

// 私有IP判断
Ip::isPrivate('192.168.1.1'); // true
Ip::isPublic('8.8.8.8');      // true
```

## 全局辅助函数

使用`composer require`后，全局函数自动加载：

```php
// 数组函数
arr_first([1, 2, 3]);           // 1
arr_last([1, 2, 3]);            // 3
arr_find([1, 2, 3], fn($n) => $n > 1); // 2

// 字符串函数
str_mask_phone('13800138000'); // 138****8000
str_to_base64('hello');        // aGVsbG8=

// 时间函数
time_now();                    // 当前时间
time_human('2024-01-01');     // 3天前

// 数学函数
math_add('1.1', '2.2');        // 3.3000000000
math_discount('100', '0.8');  // 80

// 地理位置函数
geo_distance(39.9042, 116.4074, 31.2304, 121.4737); // 距离（米）

// IP函数
ip_get();                      // 获取客户端IP
```

## 许可证

MIT License
