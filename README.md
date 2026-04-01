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
* [二维码模块](#二维码模块)
* [全局辅助函数](#全局辅助函数)
* [许可证](#许可证)

<!-- TOC -->

## 简介

这是一个基于PHP8.1+特性开发的模块化通用工具包，提供了消息体、数组处理、字符串处理、时间处理、加解密、IP地址处理、地理计算、HTTP请求、二维码生成、全局辅助方法等功能。支持对象和静态两种调用方式。

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
// 结果: ['code' => 200, 'msg' => '成功']

// 指定状态码和消息
Message::code(20001)->msg('请求数据有误')->result();
// 结果: ['code' => 20001, 'msg' => '请求数据有误']

// 传入data数据（无data不显示data字段）
Message::data(['id' => 1])->result();
// 结果: ['code' => 200, 'msg' => '成功', 'data' => ['id' => 1]]

// 链式调用位置不约束
Message::data(['id' => 1])->code(20001)->msg('请求数据有误')->result();
// 结果: ['code' => 20001, 'msg' => '请求数据有误', 'data' => ['id' => 1]]

// 动态添加任意字段
Message::data(['id' => 1])->page(1)->name('张三')->result();
// 结果: ['code' => 200, 'msg' => '成功', 'data' => ['id' => 1], 'page' => 1, 'name' => '张三']
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Message::result()` | 返回结果数组，默认code=200, msg="成功" | `Message::result()` |
| `Message::code(int $code)` | 设置状态码 | `Message::code(400)` |
| `Message::msg(string $msg)` | 设置消息文本 | `Message::msg('成功')` |
| `Message::data(mixed $data)` | 设置数据（可选） | `Message::data(['id' => 1])` |
| `Message::page(int $page)` | 设置页码（动态字段） | `Message::page(1)` |
| `Message::total(int $total)` | 设置总数（动态字段） | `Message::total(100)` |
| `Message::loadCodes(string $path)` | 从文件加载状态码 | `Message::loadCodes('config/codes.php')` |
| `Message::codes(array $codes)` | 合并自定义状态码 | `Message::codes([800000 => '自定义错误'])` |

### 内置状态码

```php
// HTTP标准码
200 => 'OK', 400 => 'Bad Request', 401 => 'Unauthorized',
403 => 'Forbidden', 404 => 'Not Found', 500 => 'Internal Server Error'

// 业务码
300000 => 'Token无效', 300001 => 'Token已过期',
400000 => '参数错误', 500000 => '数据库错误'
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

// AES加密解密（密钥至少16字符）
$crypto = new Crypto('mykey1234567890ab');  // 17字符密钥
$encrypted = $crypto->encrypt('敏感数据');
// 加密结果: JeB6LDA+LM9Yu87injOYd0ToSPfb4f/XBcEv7/qI6TWDczdBd3...

$decrypted = $crypto->decrypt($encrypted);
// 解密结果: 敏感数据

// 特殊字符/中文/emoji完全支持
$crypto->encrypt('你好世界 🌍');
// 加密结果: DKJaDjUoVt8mHZHlNba/XKk1pRY9dYV23kR2FTPkhnq69Er2OP...

// MD5哈希（加盐）
Crypto::md5('123456', 'salt');
// 结果: 2e4475e67a7d80f8c1e5c3f5e5c3a8f5

// 密码哈希
$hash = Crypto::passwordHash('123456');
// 结果: $2y$10$LQv3c1yqBwe...（随机哈希）
Crypto::passwordVerify('123456', $hash);
// 结果: true

// UUID/Token生成
Crypto::uuid();
// 结果: 58d1b3c9-5ec4-4356-8007-e7ec469d1dae
Crypto::token(32);
// 结果: 89eb2398743a88f5fdcf33c727242a3d

// HMAC签名
Crypto::hmac('数据', '密钥', 'sha256');
// 结果: 3d5f8e2b1a9c4d7e6f8a3b2c1d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f
```

### 特殊字符支持

加解密功能完美支持以下特殊字符：

| 类型 | 示例 | 支持状态 |
|------|------|----------|
| 中文 | `你好世界` | ✅ 完全支持 |
| Emoji | `😀🎉🚀` | ✅ 完全支持 |
| 特殊符号 | `!@#$%^&*()_+-=[]{}` | ✅ 完全支持 |
| HTML标签 | `<script>alert("xss")</script>` | ✅ 完全支持 |
| JSON字符串 | `{"key":"value"}` | ✅ 完全支持 |
| 多行文本 | `第一行\n第二行\r\n` | ✅ 完全支持 |
| 空字符 | `前端\0后端` | ✅ 完全支持 |
| SQL注入 | `'; DROP TABLE users; --` | ✅ 完全支持 |

### 加密引擎

自动检测并选择最优加密引擎：

| 引擎 | 算法 | 说明 |
|------|------|------|
| Sodium (推荐) | AES-256-GCM | 优先使用（PHP8.5+） |
| Sodium Fallback | ChaCha20-Poly1305 | Sodium不支持AES-GCM时自动切换 |
| OpenSSL | AES-256-GCM | Sodium不可用时使用 |

### 输出模式

| 模式 | 说明 | 适用场景 |
|------|------|----------|
| `MODE_STANDARD` | 标准Base64 | 默认，数据存储 |
| `MODE_URL_SAFE` | URL安全Base64 | URL参数传输 |
| `MODE_COMPACT` | 十六进制 | 日志记录、调试 |

### URL传输加解密

URL传输时需要使用`MODE_URL_SAFE`模式，避免`+/=`等字符被URL编码：

```php
use Kode\Crypto\Crypto;

// 创建URL安全加密实例
$crypto = new Crypto('your_secret_key_16chars');
$crypto->mode(Crypto::MODE_URL_SAFE);

// 加密数据
$original = '{"user_id":123,"token":"jwt..."}';
$encrypted = $crypto->encrypt($original);
// 加密结果: r50Mzv6rf1t5q6AJvQt5Pc-Zqy5ykcrgX8jSEgGi...

// 构建URL
$url = 'https://api.example.com/user?' . http_build_query(['data' => $encrypted]);

// 服务器端解密
$received = $_GET['data'] ?? $encrypted;
$decrypted = $crypto->decrypt($received);
// 解密结果: {"user_id":123,"token":"jwt..."}
```

### 安全防护

#### XSS防护

使用`Str::xssSafe()`防护XSS攻击：

```php
use Kode\String\Str;

// XSS攻击示例
$attacks = [
    '<script>alert("XSS")</script>',
    'javascript:alert("XSS")',
    '<img src=x onerror=alert("XSS")>',
];

// 防护处理
foreach ($attacks as $input) {
    $safe = Str::xssSafe($input);
    echo $safe;
    // 输出: &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;
}
```

#### SQL注入防护

使用`Str::sqlSafe()`防护SQL注入：

```php
use Kode\String\Str;

$userInput = "'; DROP TABLE users; --";
$safe = Str::sqlSafe($userInput);
// 输出: \'\'; DROP TABLE users; --

// 严格模式（去除所有非字母数字字符）
$strict = Str::sqlSafe($userInput, true);
// 输出: DROPTABLEUSERS
```

#### HTML转义

使用`Str::htmlEscape()`转义HTML特殊字符：

```php
use Kode\String\Str;

$html = '<div class="test">Hello & "World"</div>';
$escaped = Str::htmlEscape($html);
// 输出: &lt;div class=&quot;test&quot;&gt;Hello &amp; &quot;World&quot;&lt;/div&gt;
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Crypto::md5(string $str, string $salt)` | MD5哈希（可选加盐） | `Crypto::md5('123', 'salt')` |
| `Crypto::sha1(string $str, string $salt)` | SHA1哈希（可选加盐） | `Crypto::sha1('123', 'salt')` |
| `Crypto::sha256(string $str, string $salt)` | SHA256哈希（可选加盐） | `Crypto::sha256('123', 'salt')` |
| `Crypto::hash(string $data, string $algo)` | 通用哈希 | `Crypto::hash('data', 'sha512')` |
| `Crypto::passwordHash(string $str)` | 密码哈希 | `Crypto::passwordHash('123')` |
| `Crypto::passwordVerify(string $str, string $hash)` | 密码验证 | `Crypto::passwordVerify('123', $hash)` |
| `Crypto::hmac(string $data, string $key, string $algo)` | HMAC签名 | `Crypto::hmac('data', 'key')` |
| `Crypto::hashEquals(string $known, string $user)` | 恒时比较 | `Crypto::hashEquals($a, $b)` |
| `Crypto::token(int $length)` | 生成随机Token | `Crypto::token(32)` |
| `Crypto::uuid()` | 生成UUID | `Crypto::uuid()` |
| `Crypto::randomString(int $length)` | 随机字符串 | `Crypto::randomString(16)` |
| `Crypto::orderId(string $prefix)` | 订单号 | `Crypto::orderId('ORD')` |
| `Crypto::inviteCode(int $length)` | 邀请码 | `Crypto::inviteCode(6)` |
| `Crypto::verifyCode(int $length)` | 验证码 | `Crypto::verifyCode(4)` |
| `(new Crypto($key))->encrypt(string $data)` | AES加密 | `$c->encrypt('data')` |
| `(new Crypto($key))->decrypt(string $data)` | AES解密 | `$c->decrypt($encrypted)` |

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
// 响应: Response对象，可调用 $response->json() 获取数据

// POST请求
$response = Curl::post('https://api.example.com/users', ['name' => '张三'])
    ->withJson(['name' => '张三'])
    ->send();
// 响应: Response对象

// 链式调用
$response = Curl::create('https://api.example.com/users')
    ->method('POST')
    ->withJson(['name' => '张三'])
    ->authorization('Bearer xxx')
    ->send();
// 响应: Response对象

// Promise风格
$result = Curl::get('https://api.example.com/users')
    ->then(fn($response) => $response->json())
    ->catch(fn($response) => $response->getErrorMessage());
// 成功: 返回JSON数据
// 失败: 返回错误消息
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Curl::get(string $url, array $query)` | GET请求 | `Curl::get('/users', ['page' => 1])` |
| `Curl::post(string $url, mixed $data)` | POST请求 | `Curl::post('/users', ['name' => 'x'])` |
| `Curl::put(string $url, mixed $data)` | PUT请求 | `Curl::put('/users/1', $data)` |
| `Curl::delete(string $url)` | DELETE请求 | `Curl::delete('/users/1')` |
| `Curl::create(string $url)` | 创建实例 | `Curl::create('/users')` |
| `->send()` | 发送请求 | `$curl->send()` |
| `->timeout(int $seconds)` | 超时时间 | `->timeout(30)` |
| `->headers(array $headers)` | 设置请求头 | `->headers(['Auth' => 'xxx'])` |
| `->withJson(mixed $data)` | JSON数据 | `->withJson(['id' => 1])` |
| `->withForm(array $data)` | 表单数据 | `->withForm(['name' => 'x'])` |
| `->authorization(string $token)` | Bearer认证 | `->authorization('xxx')` |
| `->proxy(string $host, int $port)` | 代理设置 | `->proxy('127.0.0.1', 8080)` |
| `->retry(int $times, int $delay)` | 重试机制 | `->retry(3, 1000)` |
| `->then(callable $onFulfilled)` | 成功回调 | `->then(fn($r) => $r->json())` |
| `->catch(callable $onRejected)` | 错误回调 | `->catch(fn($r) => $r->error)` |

### Response方法

| 方法 | 说明 | 示例 |
|------|------|------|
| `->isSuccess()` | 是否成功(2xx) | `$res->isSuccess()` |
| `->isRedirect()` | 是否重定向(3xx) | `$res->isRedirect()` |
| `->isClientError()` | 客户端错误(4xx) | `$res->isClientError()` |
| `->isServerError()` | 服务端错误(5xx) | `$res->isServerError()` |
| `->isJson()` | 是否JSON | `$res->isJson()` |
| `->json()` | 获取JSON数据 | `$res->json()` |
| `->getContent()` | 获取内容 | `$res->getContent()` |
| `->getStatusCode()` | 状态码 | `$res->getStatusCode()` |

## 数组处理模块

### 特性

- ✅ 树形结构转换
- ✅ first/last/find 数组操作
- ✅ 深度合并
- ✅ 统计计算

### 快速开始

```php
use Kode\Array\Arr;

// 数组转树形
$tree = Arr::tree([
    ['id' => 1, 'parent_id' => 0, 'name' => 'A'],
    ['id' => 2, 'parent_id' => 1, 'name' => 'B'],
    ['id' => 3, 'parent_id' => 1, 'name' => 'C'],
], 'id', 'parent_id');
// 结果: [['id' => 1, 'parent_id' => 0, 'name' => 'A', 'children' => [...]]]

// 获取首尾元素
Arr::first([1, 2, 3]);
// 结果: 1
Arr::last([1, 2, 3]);
// 结果: 3

// 查找元素
Arr::find([1, 2, 3], fn($n) => $n > 1);
// 结果: 2

// 深度合并
Arr::deepMerge(['a' => 1], ['a' => 2, 'b' => 3]);
// 结果: ['a' => 2, 'b' => 3]
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Arr::tree(array $list, string $idField, string $parentIdField)` | 数组转树形 | `Arr::tree($list, 'id', 'pid')` |
| `Arr::list(array $tree, string $childrenField)` | 树形转数组 | `Arr::list($tree, 'children')` |
| `Arr::level(array $list, string $idField, string $parentIdField)` | 计算层级 | `Arr::level($list, 'id', 'pid')` |
| `Arr::path(array $list, string $idField, string $parentIdField, string $nameField)` | 获取路径 | `Arr::path($list, 'id', 'pid', 'name')` |
| `Arr::first(array $array)` | 获取第一个元素 | `Arr::first([1,2,3])` |
| `Arr::last(array $array)` | 获取最后一个元素 | `Arr::last([1,2,3])` |
| `Arr::find(array $array, callable $callback)` | 查找满足条件的元素 | `Arr::find($arr, fn($v)=>$v>1)` |
| `Arr::filter(array $array, callable $callback)` | 过滤数组 | `Arr::filter($arr, fn($v)=>$v>0)` |
| `Arr::map(array $array, callable $callback)` | 遍历修改 | `Arr::map($arr, fn($v)=>$v*2)` |
| `Arr::reduce(array $array, callable $callback, mixed $initial)` | 合并为单一值 | `Arr::reduce($arr, fn($c,$v)=>$c+$v, 0)` |
| `Arr::deepMerge(array $array1, array $array2)` | 深度合并 | `Arr::deepMerge($a1, $a2)` |
| `Arr::group(array $array, string $key)` | 分组 | `Arr::group($arr, 'category')` |
| `Arr::sort(array $array, string $key, string $order)` | 排序 | `Arr::sort($arr, 'id', 'asc')` |
| `Arr::unique(array $array)` | 去重 | `Arr::unique($arr)` |
| `Arr::chunk(array $array, int $size)` | 分块 | `Arr::chunk($arr, 10)` |
| `Arr::flatten(array $array)` | 扁平化 | `Arr::flatten($tree)` |
| `Arr::paginate(array $array, int $page, int $size)` | 分页 | `Arr::paginate($arr, 1, 10)` |
| `Arr::sum(array $array, string $key)` | 求和 | `Arr::sum($arr, 'price')` |
| `Arr::avg(array $array, string $key)` | 平均值 | `Arr::avg($arr, 'score')` |
| `Arr::max(array $array, string $key)` | 最大值 | `Arr::max($arr, 'price')` |
| `Arr::min(array $array, string $key)` | 最小值 | `Arr::min($arr, 'price')` |
| `Arr::random(array $array)` | 随机取一个 | `Arr::random($arr)` |
| `Arr::randomMany(array $array, int $num)` | 随机取多个 | `Arr::randomMany($arr, 3)` |
| `Arr::column(array $array, string $columnKey)` | 提取列 | `Arr::column($arr, 'name')` |
| `Arr::pluck(array $array, string $key)` | 提取值列表 | `Arr::pluck($arr, 'id')` |

## 字符串处理模块

### 特性

- ✅ 手机号/邮箱/身份证脱敏
- ✅ 驼峰/蛇形命名转换
- ✅ Base64编码解码
- ✅ UUID生成
- ✅ 字符串验证
- ✅ 格式转换

### 快速开始

```php
use Kode\String\Str;

// 脱敏
Str::maskPhone('13800138000');
// 结果: 138****8000
Str::maskEmail('user@example.com');
// 结果: us**@example.com

// 命名转换
Str::camel('hello_world');
// 结果: helloWorld
Str::snake('helloWorld');
// 结果: hello_world
Str::studly('hello_world');
// 结果: HelloWorld

// Base64
Str::toBase64('hello');
// 结果: aGVsbG8=
Str::fromBase64('aGVsbG8=');
// 结果: hello

// UUID
Str::uuid();
// 结果: a24e88d8-b560-4196-a34b-63626c1e489d

// 验证
Str::validatePhone('13800138000');
// 结果: true
Str::validateEmail('test@example.com');
// 结果: true
Str::validateIdCard('110101199001011234');
// 结果: true
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| **脱敏方法** |||
| `Str::maskPhone(string $phone)` | 手机号脱敏 | `Str::maskPhone('13800138000')` |
| `Str::maskEmail(string $email)` | 邮箱脱敏 | `Str::maskEmail('a@b.com')` |
| `Str::maskIdCard(string $idCard)` | 身份证脱敏 | `Str::maskIdCard('110101199001011234')` |
| `Str::maskBankCard(string $bankCard)` | 银行卡脱敏 | `Str::maskBankCard('6222021234567890')` |
| `Str::mask(string $str, int $start, int $length)` | 自定义脱敏 | `Str::mask('abc', 1, 1)` |
| **命名转换** |||
| `Str::camel(string $str)` | 转驼峰 | `Str::camel('hello_world')` |
| `Str::snake(string $str)` | 转蛇形 | `Str::snake('helloWorld')` |
| `Str::studly(string $str)` | 转大驼峰 | `Str::studly('hello_world')` |
| **编码转换** |||
| `Str::toBase64(string $str)` | 字符串转Base64 | `Str::toBase64('hello')` |
| `Str::fromBase64(string $base64)` | Base64转字符串 | `Str::fromBase64('aGVsbG8=')` |
| `Str::toJson(mixed $data)` | 转JSON | `Str::toJson(['a' => 1])` |
| `Str::fromJson(string $json)` | JSON转数据 | `Str::fromJson('{"a":1}')` |
| `Str::toXml(array $data)` | 转XML | `Str::toXml(['a' => 1])` |
| `Str::fromXml(string $xml)` | XML转数组 | `Str::fromXml('<root><a>1</a></root>')` |
| `Str::toArray(string $str, string $delimiter)` | 字符串转数组 | `Str::toArray('a,b,c')` |
| `Str::fromArray(array $array, string $delimiter)` | 数组转字符串 | `Str::fromArray(['a','b'])` |
| **字符串判断** |||
| `Str::contains(string $str, string $needle)` | 是否包含 | `Str::contains('hello', 'll')` |
| `Str::startsWith(string $str, string $prefix)` | 是否开头 | `Str::startsWith('hello', 'he')` |
| `Str::endsWith(string $str, string $suffix)` | 是否结尾 | `Str::endsWith('hello', 'lo')` |
| **字符串截取** |||
| `Str::substr(string $str, int $start, ?int $length)` | 截取字符串 | `Str::substr('hello', 0, 3)` |
| `Str::mbSubstr(string $str, int $start, ?int $length)` | 中文截取 | `Str::mbSubstr('你好', 0, 1)` |
| `Str::truncate(string $str, int $length, string $suffix)` | 按长度截断 | `Str::truncate('abc', 2)` |
| `Str::limit(string $str, int $limit, string $end)` | 按显示宽度截断 | `Str::limit('你好世界', 5)` |
| `Str::length(string $str)` | 获取长度 | `Str::length('hello')` |
| `Str::mbLength(string $str)` | 中文长度 | `Str::mbLength('你好')` |
| **字符串修改** |||
| `Str::replace(string $str, string $search, string $replace)` | 替换 | `Str::replace('a', 'b', 'a')` |
| `Str::remove(string $str, string $search)` | 删除 | `Str::remove('ab', 'b')` |
| `Str::reverse(string $str)` | 反转 | `Str::reverse('abc')` |
| `Str::shuffle(string $str)` | 随机打乱 | `Str::shuffle('abc')` |
| `Str::repeat(string $str, int $times)` | 重复 | `Str::repeat('a', 3)` |
| `Str::pad(string $str, int $length, string $pad, int $type)` | 填充 | `Str::pad('a', 5, '0', STR_PAD_LEFT)` |
| `Str::trim(string $str)` | 去空格 | `Str::trim(' abc ')` |
| `Str::ucfirst(string $str)` | 首字母大写 | `Str::ucfirst('hello')` |
| `Str::lcfirst(string $str)` | 首字母小写 | `Str::lcfirst('Hello')` |
| `Str::title(string $str)` | 首字母大写词 | `Str::title('hello world')` |
| **验证方法** |||
| `Str::validatePhone(string $phone, string $region)` | 验证手机号 | `Str::validatePhone('13800138000')` |
| `Str::validateEmail(string $email)` | 验证邮箱 | `Str::validateEmail('a@b.com')` |
| `Str::validateIdCard(string $idCard)` | 验证身份证 | `Str::validateIdCard('110101199001011234')` |
| `Str::validateCarPlate(string $plate)` | 验证车牌 | `Str::validateCarPlate('京A12345')` |
| **安全方法** |||
| `Str::sqlSafe(string $str, bool $strict)` | SQL安全 | `Str::sqlSafe('abc')` |
| `Str::xssSafe(string $str, bool $strict)` | XSS安全 | `Str::xssSafe('<script>')` |
| `Str::htmlEscape(string $str)` | HTML转义 | `Str::htmlEscape('<')` |
| `Str::htmlUnescape(string $str)` | HTML反转义 | `Str::htmlUnescape('&lt;')` |
| **其他方法** |||
| `Str::uuid()` | 生成UUID | `Str::uuid()` |
| `Str::random(int $length, string $mode)` | 随机字符串 | `Str::random(16)` |
| `Str::clean(string $str, array $options)` | 清理字符串 | `Str::clean(' abc ')` |
| `Str::similarity(string $str1, string $str2)` | 相似度 | `Str::similarity('abc', 'abd')` |
| `Str::split(string $str, string\|array $delimiters)` | 分割字符串 | `Str::split('a,b', ',')` |
| `Str::toBool(string $str)` | 转布尔 | `Str::toBool('true')` |
| `Str::toInt(string $str)` | 转整数 | `Str::toInt('123')` |
| `Str::toFloat(string $str)` | 转浮点数 | `Str::toFloat('1.23')` |
| `Str::compress(string $str, int $level)` | 压缩 | `Str::compress('很长字符串')` |
| `Str::decompress(string $str)` | 解压 | `Str::decompress($compressed)` |

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
Time::now();
// 结果: 2026-04-01 12:00:00
Time::today();
// 结果: 2026-04-01
Time::yesterday();
// 结果: 2026-03-31
Time::tomorrow();
// 结果: 2026-04-02

// 人性化显示
Time::diffForHumans('2024-01-01');
// 结果: 2年前

// 日期范围
Time::weekStart();
// 结果: 2026-03-30
Time::weekEnd();
// 结果: 2026-04-05
Time::monthStart();
// 结果: 2026-04-01
Time::monthEnd();
// 结果: 2026-04-30

// 时间戳操作
Time::add(time(), 86400);
// 结果: 明天的时间戳
Time::sub(time(), 3600);
// 结果: 一小时前的时间戳
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Time::now(string $format)` | 当前时间 | `Time::now()` |
| `Time::today(string $format)` | 今天日期 | `Time::today()` |
| `Time::yesterday(string $format)` | 昨天 | `Time::yesterday()` |
| `Time::tomorrow(string $format)` | 明天 | `Time::tomorrow()` |
| `Time::format(?int $timestamp, string $format)` | 格式化时间 | `Time::format(time(), 'Y-m-d')` |
| `Time::diffForHumans(int\|string $timestamp)` | 人性化显示 | `Time::diffForHumans('2024-01-01')` |
| `Time::add(int $timestamp, int $interval)` | 时间加秒 | `Time::add(time(), 86400)` |
| `Time::sub(int $timestamp, int $interval)` | 时间减秒 | `Time::sub(time(), 3600)` |
| `Time::diff(int $start, int $end)` | 差值(秒) | `Time::diff($t1, $t2)` |
| `Time::weekStart(?int $timestamp)` | 本周开始 | `Time::weekStart()` |
| `Time::weekEnd(?int $timestamp)` | 本周结束 | `Time::weekEnd()` |
| `Time::monthStart(?int $timestamp)` | 本月开始 | `Time::monthStart()` |
| `Time::monthEnd(?int $timestamp)` | 本月结束 | `Time::monthEnd()` |
| `Time::yearStart(?int $timestamp)` | 今年开始 | `Time::yearStart()` |
| `Time::yearEnd(?int $timestamp)` | 今年结束 | `Time::yearEnd()` |
| `Time::lastWeekStart(?int $timestamp)` | 上周开始 | `Time::lastWeekStart()` |
| `Time::lastWeekEnd(?int $timestamp)` | 上周结束 | `Time::lastWeekEnd()` |
| `Time::lastMonthStart(?int $timestamp)` | 上月开始 | `Time::lastMonthStart()` |
| `Time::lastMonthEnd(?int $timestamp)` | 上月结束 | `Time::lastMonthEnd()` |
| `Time::between(int $timestamp, int $start, int $end)` | 是否在范围内 | `Time::between(time(), $s, $e)` |
| `Time::isToday(int $timestamp)` | 是否今天 | `Time::isToday($ts)` |
| `Time::isYesterday(int $timestamp)` | 是否昨天 | `Time::isYesterday($ts)` |
| `Time::isTomorrow(int $timestamp)` | 是否明天 | `Time::isTomorrow($ts)` |
| `Time::isThisWeek(int $timestamp)` | 是否本周 | `Time::isThisWeek($ts)` |
| `Time::isThisMonth(int $timestamp)` | 是否本月 | `Time::isThisMonth($ts)` |
| `Time::isThisYear(int $timestamp)` | 是否今年 | `Time::isThisYear($ts)` |
| `Time::dayOfWeek(int $timestamp)` | 星期几(0-6) | `Time::dayOfWeek(time())` |
| `Time::dayOfWeekName(int $timestamp)` | 星期几名称 | `Time::dayOfWeekName(time())` |
| `Time::dayOfYear(int $timestamp)` | 第几天 | `Time::dayOfYear(time())` |
| `Time::weekOfYear(int $timestamp)` | 第几周 | `Time::weekOfYear(time())` |
| `Time::daysInMonth(int $month, int $year)` | 月天数 | `Time::daysInMonth(2, 2024)` |
| `Time::age(string $birthday)` | 年龄 | `Time::age('1990-01-01')` |
| `Time::toTimestamp(string $timeStr)` | 转时间戳 | `Time::toTimestamp('2024-01-01')` |
| `Time::toMillisecond(?int $timestamp)` | 毫秒时间戳 | `Time::toMillisecond()` |
| `Time::fromMillisecond(int $millisecond)` | 毫秒转时间戳 | `Time::fromMillisecond($ms)` |
| `Time::millisecond()` | 当前毫秒 | `Time::millisecond()` |
| `Time::microsecond()` | 当前微秒 | `Time::microsecond()` |
| `Time::microtime()` | 当前微秒时间 | `Time::microtime()` |
| `Time::setTimezone(string $timezone)` | 设置时区 | `Time::setTimezone('Asia/Shanghai')` |

## 数学计算模块

### 特性

- ✅ 高精度计算（bcmath）
- ✅ 折扣/税费计算
- ✅ 平均数/中位数/标准差

### 快速开始

```php
use Kode\Math\Math;

// 高精度计算
Math::add('1.1', '2.2');
// 结果: 3.3000000000
Math::sub('5.5', '3.3');
// 结果: 2.2000000000
Math::mul('1.5', '2.5');
// 结果: 3.7500000000
Math::div('10', '3');
// 结果: 3.3333333333

// 折扣计算
Math::discount('100', '0.8');
// 结果: 80

// 税费计算
Math::tax('100', '0.13');
// 结果: 13

// 统计
Math::average([1, 2, 3, 4, 5]);
// 结果: 3
Math::median([1, 2, 3, 4, 5]);
// 结果: 3
Math::mode([1, 2, 2, 3]);
// 结果: 2
Math::standardDeviation([1, 2, 3, 4, 5]);
// 结果: 1.4142135624
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| **基础运算** |||
| `Math::add(float\|int\|string $num1, float\|int\|string $num2, int $scale)` | 加法 | `Math::add('1.1', '2.2')` |
| `Math::sub(float\|int\|string $num1, float\|int\|string $num2, int $scale)` | 减法 | `Math::sub('5.5', '3.3')` |
| `Math::mul(float\|int\|string $num1, float\|int\|string $num2, int $scale)` | 乘法 | `Math::mul('1.5', '2.5')` |
| `Math::div(float\|int\|string $num1, float\|int\|string $num2, int $scale)` | 除法 | `Math::div('10', '3')` |
| `Math::mod(float\|int\|string $num1, float\|int\|string $num2)` | 取模 | `Math::mod('10', '3')` |
| `Math::pow(float\|int\|string $num, int $exponent, int $scale)` | 幂运算 | `Math::pow('2', 10)` |
| `Math::sqrt(float\|int\|string $num, int $scale)` | 平方根 | `Math::sqrt('2')` |
| `Math::abs(float\|int\|string $num)` | 绝对值 | `Math::abs('-5')` |
| `Math::round(float\|int\|string $num, int $precision)` | 四舍五入 | `Math::round('3.5')` |
| `Math::ceil(float\|int\|string $num, int $precision)` | 向上取整 | `Math::ceil('3.1')` |
| `Math::floor(float\|int\|string $num, int $precision)` | 向下取整 | `Math::floor('3.9')` |
| `Math::compare(float\|int\|string $num1, float\|int\|string $num2)` | 比较(-1/0/1) | `Math::compare('1', '2')` |
| `Math::equal(float\|int\|string $num1, float\|int\|string $num2, int $scale)` | 是否相等 | `Math::equal('1.0', '1')` |
| `Math::format(float\|int\|string $num, int $precision, bool $thousandsSeparator)` | 格式化数字 | `Math::format('1000', 2)` |
| **金融计算** |||
| `Math::discount(float\|int\|string $price, float\|int\|string $discount, int $scale)` | 折扣价 | `Math::discount('100', '0.8')` |
| `Math::tax(float\|int\|string $amount, float\|int\|string $rate, int $scale)` | 税额 | `Math::tax('100', '0.13')` |
| `Math::taxIncluded(float\|int\|string $amount, float\|int\|string $rate, int $scale)` | 含税价 | `Math::taxIncluded('100', '0.13')` |
| `Math::taxExcluded(float\|int\|string $amount, float\|int\|string $rate, int $scale)` | 不含税价 | `Math::taxExcluded('113', '0.13')` |
| `Math::percentage(float\|int\|string $part, float\|int\|string $total, int $scale)` | 百分比 | `Math::percentage('25', '100')` |
| `Math::simpleInterest(float\|int\|string $principal, float\|int\|string $rate, int $years)` | 单利 | `Math::simpleInterest('1000', '0.05', 2)` |
| `Math::compoundInterest(float\|int\|string $principal, float\|int\|string $rate, int $years)` | 复利 | `Math::compoundInterest('1000', '0.05', 2)` |
| **三角函数** |||
| `Math::sin(float\|int\|string $num, int $scale)` | 正弦 | `Math::sin('0')` |
| `Math::cos(float\|int\|string $num, int $scale)` | 余弦 | `Math::cos('0')` |
| `Math::tan(float\|int\|string $num, int $scale)` | 正切 | `Math::tan('1')` |
| `Math::asin(float\|int\|string $num, int $scale)` | 反正弦 | `Math::asin('0.5')` |
| `Math::acos(float\|int\|string $num, int $scale)` | 反余弦 | `Math::acos('0.5')` |
| `Math::atan(float\|int\|string $num, int $scale)` | 反正切 | `Math::atan('1')` |
| **对数函数** |||
| `Math::ln(float\|int\|string $num, int $scale)` | 自然对数 | `Math::ln('2.718')` |
| `Math::log10(float\|int\|string $num, int $scale)` | 常用对数 | `Math::log10('100')` |
| `Math::log(float\|int\|string $num, float\|int\|string $base, int $scale)` | 对数 | `Math::log('8', '2')` |
| `Math::rad2deg(float\|int\|string $num, int $scale)` | 弧度转角度 | `Math::rad2deg('3.14')` |
| `Math::deg2rad(float\|int\|string $num, int $scale)` | 角度转弧度 | `Math::deg2rad('180')` |
| **数论函数** |||
| `Math::factorial(int $num)` | 阶乘 | `Math::factorial(5)` |
| `Math::gcd(int $num1, int $num2)` | 最大公约数 | `Math::gcd('12', '18')` |
| `Math::lcm(int $num1, int $num2)` | 最小公倍数 | `Math::lcm('4', '6')` |
| `Math::isPrime(int $num)` | 是否质数 | `Math::isPrime(7)` |
| `Math::isEven(int $num)` | 是否偶数 | `Math::isEven(4)` |
| `Math::isOdd(int $num)` | 是否奇数 | `Math::isOdd(3)` |
| **判断函数** |||
| `Math::isPositive(float\|int\|string $num)` | 是否正数 | `Math::isPositive('1')` |
| `Math::isNegative(float\|int\|string $num)` | 是否负数 | `Math::isNegative('-1')` |
| `Math::isZero(float\|int\|string $num, int $scale)` | 是否零 | `Math::isZero('0')` |
| `Math::isValid(mixed $num)` | 是否有效数字 | `Math::isValid('123')` |
| `Math::inRange(float\|int\|string $num, float\|int\|string $min, float\|int\|string $max)` | 是否在范围内 | `Math::inRange('5', '1', '10')` |
| `Math::clamp(float\|int\|string $num, float\|int\|string $min, float\|int\|string $max)` | 限制范围 | `Math::clamp('15', '1', '10')` |
| **插值与随机** |||
| `Math::lerp(float\|int\|string $start, float\|int\|string $end, float\|int\|string $t, int $scale)` | 线性插值 | `Math::lerp('0', '100', '0.5')` |
| `Math::random(float\|int\|string $min, float\|int\|string $max, int $scale)` | 随机数 | `Math::random('1', '100')` |
| **统计函数** |||
| `Math::average(array $numbers, int $scale)` | 平均值 | `Math::average([1,2,3])` |
| `Math::median(array $numbers, int $scale)` | 中位数 | `Math::median([1,2,3,4,5])` |
| `Math::mode(array $numbers)` | 众数 | `Math::mode([1,2,2,3])` |
| `Math::standardDeviation(array $numbers, int $scale)` | 标准差 | `Math::standardDeviation([1,2,3])` |
| `Math::variance(array $numbers, int $scale)` | 方差 | `Math::variance([1,2,3])` |

## 地理位置模块

### 特性

- ✅ 两点距离计算（Haversine）
- ✅ 坐标验证
- ✅ 方位角计算

### 快速开始

```php
use Kode\Geo\Geo;

// 两点距离（默认公里）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737);
// 结果: 1068.51

// 两点距离（米）
$distanceM = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'm');
// 结果: 1068506.82

// 坐标验证
Geo::isValid(39.9042, 116.4074);
// 结果: true

// 方位角
Geo::bearing(39.9042, 116.4074, 31.2304, 121.4737);
// 结果: -141.67
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Geo::distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit)` | 两点距离 | `Geo::distance($lat1, $lon1, $lat2, $lon2)` |
| `Geo::isValid(float $lat, float $lon)` | 坐标验证 | `Geo::isValid(39.9042, 116.4074)` |
| `Geo::toRadians(float $degrees)` | 度转弧度 | `Geo::toRadians(180)` |
| `Geo::toDegrees(float $radians)` | 弧度转度 | `Geo::toDegrees(3.14)` |
| `Geo::midpoint(float $lat1, float $lon1, float $lat2, float $lon2)` | 中点坐标 | `Geo::midpoint($lat1, $lon1, $lat2, $lon2)` |
| `Geo::bearing(float $lat1, float $lon1, float $lat2, float $lon2)` | 方位角(度) | `Geo::bearing($lat1, $lon1, $lat2, $lon2)` |
| `Geo::toDMS(float $decimal, bool $isLatitude)` | 十进制转度分秒 | `Geo::toDMS(39.9042, true)` |
| `Geo::toDecimal(int $degrees, int $minutes, float $seconds, string $direction)` | 度分秒转十进制 | `Geo::toDecimal(39, 54, 15.12, 'N')` |

## IP地址模块

### 特性

- ✅ IP验证
- ✅ 私有/公网IP判断
- ✅ IP类型检测

### 快速开始

```php
use Kode\Ip\Ip;

// IP验证
Ip::isValid('192.168.1.1');
// 结果: true

// 私有IP判断
Ip::isPrivate('192.168.1.1');
// 结果: true
Ip::isPublic('8.8.8.8');
// 结果: true

// 获取客户端IP
Ip::get();
// 结果: 127.0.0.1
Ip::getRealIp();
// 结果: 真实IP

// IP类型
Ip::getType('192.168.1.1');
// 结果: private
Ip::getType('8.8.8.8');
// 结果: public

// IP转长整数
Ip::toLong('192.168.1.1');
// 结果: 3232235777
Ip::fromLong(3232235777);
// 结果: 192.168.1.1
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Ip::get(bool $checkProxy)` | 获取客户端IP | `Ip::get()` |
| `Ip::getRealIp()` | 获取真实IP | `Ip::getRealIp()` |
| `Ip::isValid(string $ip)` | IP验证 | `Ip::isValid('192.168.1.1')` |
| `Ip::isPrivate(string $ip)` | 是否私有IP | `Ip::isPrivate('192.168.1.1')` |
| `Ip::isPublic(string $ip)` | 是否公网IP | `Ip::isPublic('8.8.8.8')` |
| `Ip::getVersion(string $ip)` | IP版本(4/6) | `Ip::getVersion('192.168.1.1')` |
| `Ip::getType(string $ip)` | IP类型 | `Ip::getType('192.168.1.1')` |
| `Ip::toLong(string $ip)` | IP转整数 | `Ip::toLong('192.168.1.1')` |
| `Ip::fromLong(int $long)` | 整数转IP | `Ip::fromLong(3232235777)` |
| `Ip::getLocation(string $ip, ?string $apiKey)` | IP归属地 | `Ip::getLocation('8.8.8.8')` |
| `Ip::isFromCountry(string $ip, string $countryCode)` | 是否某国IP | `Ip::isFromCountry('8.8.8.8', 'US')` |

## 二维码模块

### 特性

- ✅ 生成二维码
- ✅ 支持Logo
- ✅ 多种编码模式

### 快速开始

```php
use Kode\Qrcode\Qr;

// 生成二维码图片
$qr = Qr::text('https://example.com')
    ->size(300)
    ->margin(10)
    ->generate();

// 保存到文件
Qr::text('https://example.com')
    ->save('qrcode.png');

// 生成Base64
$base64 = Qr::text('https://example.com')
    ->base64();
```

### 方法说明

| 方法 | 说明 | 示例 |
|------|------|------|
| `Qr::text(string $content)` | 设置内容 | `Qr::text('hello')` |
| `Qr::size(int $size)` | 二维码尺寸 | `Qr::size(300)` |
| `Qr::margin(int $margin)` | 边距 | `Qr::margin(10)` |
| `Qr::foregroundColor(string $color)` | 前景色 | `Qr::foregroundColor('#000000')` |
| `Qr::backgroundColor(string $color)` | 背景色 | `Qr::backgroundColor('#ffffff')` |
| `Qr::logo(string $path)` | Logo路径 | `Qr::logo('logo.png')` |
| `Qr::generate()` | 生成图片 | `$qr->generate()` |
| `Qr::save(string $path)` | 保存文件 | `$qr->save('qr.png')` |
| `Qr::base64()` | 返回Base64 | `$qr->base64()` |

## 全局辅助函数

使用`composer require`后，全局函数自动加载：

```php
// 数组函数
arr_first([1, 2, 3]);
// 结果: 1
arr_last([1, 2, 3]);
// 结果: 3
arr_find([1, 2, 3], fn($n) => $n > 1);
// 结果: 2
arr_random([1, 2, 3]);
// 结果: 随机一个元素

// 字符串函数
str_mask_phone('13800138000');
// 结果: 138****8000
str_mask_email('a@b.com');
// 结果: a***@b.com
str_to_base64('hello');
// 结果: aGVsbG8=
str_from_base64('aGVsbG8=');
// 结果: hello
str_truncate('abcdef', 3);
// 结果: abc...
str_camel('hello_world');
// 结果: helloWorld
str_uuid();
// 结果: a24e88d8-b560-4196-a34b-63626c1e489d

// 时间函数
time_now();
// 结果: 2026-04-01 12:00:00
time_today();
// 结果: 2026-04-01
time_diff_for_humans('2024-01-01');
// 结果: 2年前

// 数学函数
math_add('1.1', '2.2');
// 结果: 3.3000000000
math_sub('5.5', '3.3');
// 结果: 2.2000000000
math_discount('100', '0.8');
// 结果: 80
math_tax('100', '0.13');
// 结果: 13
math_average([1, 2, 3]);
// 结果: 2

// 地理位置函数
geo_distance(39.9042, 116.4074, 31.2304, 121.4737);
// 结果: 1068.51
geo_is_valid(39.9042, 116.4074);
// 结果: true

// IP函数
ip_get();
// 结果: 127.0.0.1
ip_is_valid('192.168.1.1');
// 结果: true
ip_is_private('192.168.1.1');
// 结果: true

// 辅助函数
uuid();
// 结果: a24e88d8-b560-4196-a34b-63626c1e489d
random_string(16);
// 结果: xK7d9f2mAb3cL5nP
```

## 许可证

MIT License
