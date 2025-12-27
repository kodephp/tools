# kode/tools - PHP8.3+ 通用工具包

<!-- TOC -->

* [简介](#简介)
* [核心特性](#核心特性)
* [安装](#安装)
* [模块结构建议](#模块结构建议)
* [数组处理模块使用示例](#数组处理模块使用示例)
* [字符串处理模块使用示例](#字符串处理模块使用示例)
* [时间处理模块使用示例](#时间处理模块使用示例)
* [加解密模块使用示例](#加解密模块使用示例)
* [代码生成模块使用示例](#代码生成模块使用示例)
* [数学计算模块使用示例](#数学计算模块使用示例)
* [地理位置模块使用示例](#地理位置模块使用示例)
* [IP地址模块使用示例](#ip地址模块使用示例)
* [消息体模块使用示例](#消息体模块使用示例)
* [HTTP请求模块使用示例](#http请求模块使用示例)
* [二维码生成模块使用示例](#二维码生成模块使用示例)
* [全局助手函数使用示例](#全局助手函数使用示例)
* [许可证](#许可证)

<!-- TOC -->

## 简介

这是一个基于PHP8.3+特性开发的模块化通用工具包，提供了数组处理、字符串处理、时间处理、加解密、消息体、IP地址处理、地理计算、全局辅助方法等功能。支持对象和静态两种调用方式，兼容进程和协程环境。

## 核心特性

### 模块化架构
工具包采用清晰的模块化架构，每个模块专注于特定领域的功能：

```
├── src/
│   ├── Message/          // 消息体核心
│   │   ├── Message.php   // 消息体主类（链式调用）
│   │   ├── CodeMap.php   // 状态码映射配置类
│   ├── Crypto/           // 加解密核心
│   │   ├── Crypto.php    // 加解密主类（命名简洁）
│   │   ├── Contracts/    // 接口定义
│   │   │   ├── EncryptInterface.php
│   │   │   ├── DecryptInterface.php
│   ├── Array/            // 数组处理核心
│   │   ├── Arr.php       // 数组处理主类
│   ├── String/           // 字符串处理核心
│   │   ├── Str.php       // 字符串处理主类
│   ├── Time/             // 时间处理核心
│   │   ├── Time.php      // 时间处理主类
│   ├── Math/             // 数学计算核心
│   │   ├── Math.php      // 数学计算主类
│   ├── Geo/              // 地理位置核心
│   │   ├── Geo.php       // 地理位置主类
│   ├── Ip/               // IP地址处理核心
│   │   ├── Ip.php        // IP地址处理主类
│   ├── Curl/             // HTTP请求核心
│   │   ├── Curl.php      // HTTP请求主类
│   │   ├── Response.php  // 响应处理类
│   │   ├── Exception/    // 异常类
│   │   │   ├── CurlException.php
│   │   │   ├── ClientException.php
│   │   │   └── ServerException.php
│   ├── Qrcode/           // 二维码生成核心
│   │   ├── Qrcode.php    // 二维码主类
│   │   ├── Color.php     // 颜色类
│   │   ├── LogoPosition.php   // Logo位置枚举
│   │   ├── RoundBlockSizeMode.php // 圆角模式枚举
│   │   └── EncodingMode.php     // 编码模式枚举
│   ├── Helper/           // 全局辅助函数
│   │   ├── helper.php    // 全局辅助函数文件
├── composer.json         // Composer配置
├── README.md             // 使用文档
```

### 数组处理模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`first()`](#first---数组首元素) | 获取第一个元素 | `Arr::first([1, 2, 3])` |
| [`last()`](#last---数组尾元素) | 获取最后一个元素 | `Arr::last([1, 2, 3])` |
| [`find()`](#find---查找满足条件的元素) | 查找满足条件的元素 | `Arr::find([1, 2, 3], fn($n) => $n > 1)` |
| [`tree()`](#tree---数组转树形结构) | 数组转树形结构 | `Arr::tree($list, 'id', 'pid')` |
| [`list()`](#list---树形结构转数组) | 树形结构转数组 | `Arr::list($tree)` |
| [`deepMerge()`](#deepmerge---数组深度合并) | 深度合并数组 | `Arr::deepMerge($arr1, $arr2)` |

#### 特性
- ✅ 树形结构转换（数组转树、树转数组）
- ✅ 层级结构转换
- ✅ 路径结构转换
- ✅ 常用数组操作（get/set/has/only/except）
- ✅ 数组深度合并
- ✅ 多维数组分组
- ✅ 多维数组统计（count/sum/avg/max/min）
- ✅ 多维数组转JSON/JSON转数组
- ✅ 非递归算法，支持大数据量
- ✅ 支持点语法和数组嵌套键访问

### 字符串处理模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`maskPhone()`](#maskphone---手机号脱敏) | 手机号脱敏 | `Str::maskPhone('13800138000')` |
| [`maskEmail()`](#maskemail---邮箱脱敏) | 邮箱脱敏 | `Str::maskEmail('user@example.com')` |
| [`camel()`](#camel---下划线转驼峰) | 转驼峰命名 | `Str::camel('hello_world')` |
| [`snake()`](#snake---驼峰转下划线) | 转蛇形命名 | `Str::snake('helloWorld')` |
| [`toBase64()`](#tobase64---转base64编码) | 转Base64编码 | `Str::toBase64('hello')` |
| [`fromBase64()`](#frombase64---base64解码) | Base64解码 | `Str::fromBase64('aGVsbG8=')` |

#### 特性
- ✅ 随机字符串生成
- ✅ UUID生成
- ✅ 命名转换（驼峰、蛇形、大驼峰）
- ✅ 字符串转义和反转义
- ✅ 字符串修剪
- ✅ 字符串脱敏（手机号、身份证号、邮箱、银行卡号、车牌号、姓名）
- ✅ 字符串验证（手机号、身份证号、邮箱、车牌号、银行卡号）
- ✅ 收货地址解析
- ✅ 字符串格式化和转换
- ✅ 字符串相似度比较
- ✅ 多分隔符字符串分割

### 时间处理模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`format()`](#format---格式化时间) | 格式化时间 | `Time::format(time(), 'Y-m-d')` |
| [`now()`](#now---获取当前时间) | 获取当前时间 | `Time::now()` |
| [`today()`](#today---获取今天日期) | 获取今天日期 | `Time::today()` |
| [`yesterday()`](#yesterday---获取昨天日期) | 获取昨天日期 | `Time::yesterday()` |
| [`tomorrow()`](#tomorrow---获取明天日期) | 获取明天日期 | `Time::tomorrow()` |
| [`add()`](#add---时间加法) | 时间加法 | `Time::add(time(), 3600)` |
| [`sub()`](#sub---时间减法) | 时间减法 | `Time::sub(time(), 3600)` |
| [`diff()`](#diff---时间差) | 时间差 | `Time::diff(time(), time() - 3600)` |
| [`diffForHumans()`](#diffforhumans---人性化时间差) | 人性化时间差 | `Time::diffForHumans(time() - 3600)` |
| [`weekStart()`](#weekstart---获取本周开始时间) | 本周开始时间 | `Time::weekStart()` |
| [`weekEnd()`](#weekend---获取本周结束时间) | 本周结束时间 | `Time::weekEnd()` |
| [`monthStart()`](#monthstart---获取本月开始时间) | 本月开始时间 | `Time::monthStart()` |
| [`monthEnd()`](#monthend---获取本月结束时间) | 本月结束时间 | `Time::monthEnd()` |

#### 特性
- ✅ 时间格式化
- ✅ 时间计算（加法、减法、差值）
- ✅ 常用时间获取（当前时间、今天、昨天、明天）
- ✅ 时区支持
- ✅ 人性化时间显示、日期范围计算

### 加解密模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`cryptoMd5()`](#cryptomd5---md5加密支持加盐) | MD5加密（支持加盐） | `Crypto::cryptoMd5('123456', 'salt')` |
| [`cryptoPasswordHash()`](#cryptopasswordhash---密码哈希) | 密码哈希 | `Crypto::cryptoPasswordHash('123456')` |
| [`cryptoPasswordVerify()`](#cryptopasswordverify---密码验证) | 密码验证 | `Crypto::cryptoPasswordVerify('123456', $hash)` |
| [`cryptoSslEncrypt()`](#cryptossleencrypt---ssl对称加密) | SSL对称加密 | `Crypto::cryptoSslEncrypt('data', $key)` |
| [`cryptoSslDecrypt()`](#cryptosslecrypt---ssl对称解密) | SSL对称解密 | `Crypto::cryptoSslDecrypt($encrypted, $key)` |

#### 特性
- ✅ MD5加密（支持加盐）
- ✅ 密码哈希（基于PHP原生password_hash）
- ✅ SSL对称加密（AES-256-GCM）
- ✅ HMAC签名（支持多种算法）
- ✅ 双模式调用：实例调用 + 静态调用
- ✅ 符合安全最佳实践
- ✅ PHP8.3+只读属性确保配置安全

### 代码生成模块
- ✅ 订单号生成（时间戳+随机数）
- ✅ 邀请码生成（自定义长度和字符集）
- ✅ URL安全码生成（Base64URL编码）
- ✅ 注册码生成（支持分段显示）
- ✅ 线程安全的随机数生成（random_int）

### 数学计算模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`add()`](#add---高精度加法) | 高精度加法 | `Math::add('1.1', '2.2')` |
| [`sub()`](#sub---高精度减法) | 高精度减法 | `Math::sub('3.3', '1.1')` |
| [`mul()`](#mul---高精度乘法) | 高精度乘法 | `Math::mul('2.5', '4')` |
| [`div()`](#div---高精度除法) | 高精度除法 | `Math::div('10', '3')` |
| [`avg()`](#avg---多维数组求平均值) | 平均数 | `Math::avg([1, 2, 3, 4, 5])` |
| [`median()`](#median---中位数) | 中位数 | `Math::median([1, 2, 3, 4, 5])` |
| [`discount()`](#discount---折扣计算) | 计算折扣价 | `Math::discount('100', '0.2')` |
| [`tax()`](#tax---税费计算) | 计算税额 | `Math::tax('100', '0.13')` |

#### 特性
- ✅ 高精度计算（使用bcmath扩展）
- ✅ 支持金融计算（折扣、税费、利息）
- ✅ 支持统计分析（平均数、中位数、标准差）
- ✅ 支持幂运算、平方根等数学操作
- ✅ 结果保留任意小数位数
- ✅ 兼容PHP8.3+特性

### 地理位置模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`distance()`](#distance---计算两个坐标之间的距离) | 计算两点距离 | `Geo::distance(39.9042, 116.4074, 31.2304, 121.4737)` |
| [`isValidCoordinate()`](#isvalidcoordinate---坐标验证) | 验证坐标 | `Geo::isValidCoordinate(39.9042, 116.4074)` |
| [`wgs84ToGcj02()`](#wgs84togcj02---坐标转换wgs84转gcj02) | WGS84转GCJ02 | `Geo::wgs84ToGcj02(39.9042, 116.4074)` |
| [`gcj02ToBd09()`](#gcj02tobd09---坐标转换gcj02转bd09) | GCJ02转BD09 | `Geo::gcj02ToBd09(39.9042, 116.4074)` |
| [`getBearing()`](#getbearing---计算方位角) | 计算方位角 | `Geo::getBearing(39.9042, 116.4074, 31.2304, 121.4737)` |
| [`getMidpoint()`](#getmidpoint---计算中点坐标) | 计算中点坐标 | `Geo::getMidpoint(39.9042, 116.4074, 31.2304, 121.4737)` |

#### 特性
- ✅ 使用Haversine公式计算距离
- ✅ 支持多种距离单位（公里、英里、米）
- ✅ 支持坐标转换（WGS84/GCJ02/BD09）
- ✅ 支持方位角和中点计算
- ✅ 高精度计算（保留6位小数）
- ✅ 兼容PHP8.3+特性
- ✅ 高精度数学计算（加减乘除、取模、幂运算、平方根）
- ✅ 四舍五入、向上取整、向下取整
- ✅ 数值比较和格式化
- ✅ 解决浮点数精度丢失问题
- ✅ 支持PHP8.3+类型声明
- ✅ 线程安全的初始化模式
- ✅ 高精度计算、金融计算、统计分析

### 消息体模块
- ✅ 双模式链式调用：实例链式 + 静态链式
- ✅ 完整的状态码管理（200/400/500基础码 + 6位业务码）
- ✅ 灵活的字段扩展和映射
- ✅ 内置分页支持
- ✅ 支持JSON直接输出
- ✅ PHP8.3+特性优化：只读属性、类型细化、nullsafe运算符
- ✅ 线程安全的初始化模式

### Curl模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`get()`](#get---get请求) | GET请求 | `Curl::get('https://api.example.com')->send()` |
| [`post()`](#post---post请求) | POST请求 | `Curl::post('https://api.example.com', $data)->send()` |
| [`put()`](#put---put请求) | PUT请求 | `Curl::put('https://api.example.com/1', $data)->send()` |
| [`delete()`](#delete---delete请求) | DELETE请求 | `Curl::delete('https://api.example.com/1')->send()` |
| [`patch()`](#patch---patch请求) | PATCH请求 | `Curl::patch('https://api.example.com/1', $data)->send()` |
| [`head()`](#head---head请求) | HEAD请求 | `Curl::head('https://api.example.com')->send()` |
| [`options()`](#options---options请求) | OPTIONS请求 | `Curl::options('https://api.example.com')->send()` |
| [`multi()`](#multi---并发执行多个请求) | 并发请求 | `Curl::multi()->add()->execute()` |
| [`retry()`](#retry---自动重试) | 自动重试 | `Curl::get()->retry(3, 2.0)` |
| [`body()`](#body---获取原始响应体) | 获取响应体 | `$response->body()` |
| [`json()`](#json---获取json解析结果) | 获取JSON | `$response->json()` |
| [`header()`](#header---获取响应头) | 获取响应头 | `$response->header('Content-Type')` |
| [`statusCode()`](#statuscode---获取响应状态码) | 获取状态码 | `$response->statusCode()` |

#### 特性
- ✅ 支持HTTP/HTTPS协议
- ✅ 支持GET/POST/PUT/DELETE/PATCH/HEAD/OPTIONS请求
- ✅ 支持请求超时设置
- ✅ 支持请求头设置
- ✅ 支持请求参数设置
- ✅ 支持响应状态码、响应头、响应体获取
- ✅ 支持异常处理
- ✅ 支持多种HTTP方法（GET/POST/PUT/PATCH/DELETE）
- ✅ 支持多种内容类型（JSON/form/multipart）
- ✅ 灵活的请求选项配置
- ✅ 响应处理和错误处理
- ✅ 并发请求支持
- ✅ PHP 8.5+持久化句柄支持（curl_share_init_persistent）
- ✅ 重试机制和超时控制
- ✅ SSL验证和代理支持
- ✅ 双模式调用：实例调用 + 静态调用

### Qrcode模块

#### 核心方法

| 方法名 | 功能描述 | 调用示例 |
|--------|----------|----------|
| [`create()`](#create---创建二维码) | 创建二维码 | `Qr::create('Hello World')` |
| [`size()`](#size---设置二维码大小) | 设置大小 | `Qr::create('Hello')->size(300)` |
| [`margin()`](#margin---设置边距) | 设置边距 | `Qr::create('Hello')->margin(20)` |
| [`foregroundColor()`](#foregroundcolor---设置前景色) | 设置前景色 | `Qr::create('Hello')->foregroundColor(255, 0, 0)` |
| [`backgroundColor()`](#backgroundcolor---设置背景色) | 设置背景色 | `Qr::create('Hello')->backgroundColor(255, 255, 255)` |
| [`errorCorrectionLevel()`](#errorcorrectionlevel---设置错误纠正级别) | 设置错误纠正级别 | `Qr::create('Hello')->errorCorrectionLevel(5)` |
| [`logo()`](#logo---添加logo) | 添加Logo | `Qr::create('Hello')->logo('path/to/logo.png')` |
| [`label()`](#label---添加标签) | 添加标签 | `Qr::create('Hello')->label('标签文字')` |
| [`save()`](#save---保存为png格式) | 保存为文件 | `Qr::create('Hello')->save('qrcode.png')` |
| [`toString()`](#tostring---输出图片数据) | 输出图片数据 | `$qr->toString()` |
| [`toDataUri()`](#todatauri---输出base64编码) | 生成Base64编码 | `$qr->toDataUri()` |
| [`asSvg()`](#assvg---输出svg格式) | 输出SVG格式 | `Qr::create('Hello')->asSvg()` |
| [`asWebP()`](#aswebp---输出webp格式) | 输出WebP格式 | `Qr::create('Hello')->asWebP()` |
| [`asEps()`](#aseps---输出eps格式) | 输出EPS格式 | `Qr::create('Hello')->asEps()` |
| [`url()`](#url---url二维码) | URL二维码 | `Qr::url('https://example.com')` |
| [`wifi()`](#wifi---wifi二维码) | WiFi二维码 | `Qr::wifi('MyWiFi', 'password', 'wpa')` |
| [`email()`](#email---邮件二维码) | 邮件二维码 | `Qr::email('user@example.com', '主题', '内容')` |
| [`phone()`](#phone---电话二维码) | 电话二维码 | `Qr::phone('13800138000')` |
| [`sms()`](#sms---短信二维码) | 短信二维码 | `Qr::sms('13800138000', '短信内容')` |
| [`geo()`](#geo---位置二维码) | 位置二维码 | `Qr::geo(39.9042, 116.4074)` |
| [`bitcoin()`](#bitcoin---比特币二维码) | 比特币二维码 | `Qr::bitcoin('address', 0.5)` |
| [`event()`](#event---日历事件二维码) | 日历事件二维码 | `Qr::event('标题', '开始时间', '结束时间', '地点')` |

#### 特性
- ✅ 支持多种二维码大小
- ✅ 支持多种错误纠正级别
- ✅ 支持添加Logo
- ✅ 支持自定义颜色
- ✅ 支持多种输出格式（PNG、SVG、EPS）
- ✅ 支持Base64编码输出
- ✅ 基础二维码生成（文本、URL）
- ✅ 多种样式定制：
  - ✅ 圆角点样式（圆形、圆角方形）
  - ✅ 渐变颜色支持（水平/垂直/对角线方向）
  - ✅ 自定义前景/背景颜色
  - ✅ Logo嵌入支持
  - ✅ 标签文字支持
- ✅ 多种输出格式（PNG/SVG/WebP/EPS）
- ✅ 多种数据类型支持：
  - ✅ URL二维码
  - ✅ WiFi二维码
  - ✅ 邮件二维码
  - ✅ 电话二维码
  - ✅ 短信二维码
  - ✅ 名片二维码（vCard）
  - ✅ 位置二维码
  - ✅ 比特币二维码
  - ✅ 日历事件二维码
- ✅ 多种错误纠正级别（L/M/Q/H）
- ✅ PHP8.3+特性优化：只读属性、类型声明
- ✅ 双模式调用：实例调用 + 静态调用
- ✅ 全局助手函数支持

### 全局助手函数
- ✅ 简化调用方式，无需实例化
- ✅ 自动加载，无需手动导入
- ✅ 支持所有核心功能的快捷调用

## 安装

### 1. Composer 安装

```bash
composer require kode/tools
```

### 2. 手动安装

将 src/ 目录下的文件复制到你的项目中，并配置自动加载。

## 模块结构建议

### 当前架构的优势

当前的模块化架构具有以下优势：

1. **职责单一**：每个模块专注于特定领域的功能，代码更清晰，维护更方便
2. **解耦性高**：模块之间依赖关系明确，便于独立测试和升级
3. **可扩展性强**：可以轻松添加新的模块或扩展现有模块
4. **性能优化**：按需加载，减少不必要的资源占用
5. **团队协作**：多人开发时可以并行处理不同模块

### 最佳实践

1. **保持模块独立性**：每个模块应尽量减少对其他模块的依赖
2. **统一命名规范**：所有模块采用一致的命名方式和编码风格
3. **文档化**：为每个模块和方法编写清晰的文档
4. **测试覆盖**：为每个模块编写单元测试
5. **版本控制**：定期更新和维护模块版本

## 数组处理模块使用示例

数组处理模块提供了丰富的数组操作功能，支持PHP 8.4+原生数组函数，自动根据PHP版本选择最优实现。

### PHP 8.4兼容性说明

数组处理模块会自动检测PHP版本，在PHP 8.4+环境下使用原生数组函数以获得最佳性能：

- `array_first()` - 获取数组第一个元素
- `array_last()` - 获取数组最后一个元素
- `array_find()` - 查找满足条件的元素
- `array_find_key()` - 查找满足条件的键名
- `array_any()` - 检查是否存在满足条件的元素
- `array_all()` - 检查是否所有元素都满足条件

在PHP 8.4以下版本，模块会使用兼容的实现，确保代码在不同PHP版本下都能正常运行。

### 树形结构转换

#### tree() - 数组转树形结构

```php
use Kode\Array\Arr;

// 数组转树形结构
$flatArray = [
    ['id' => 1, 'name' => '部门1', 'parent_id' => 0],
    ['id' => 2, 'name' => '部门2', 'parent_id' => 1],
    ['id' => 3, 'name' => '部门3', 'parent_id' => 1],
    ['id' => 4, 'name' => '部门4', 'parent_id' => 2]
];

$tree = Arr::tree($flatArray, 'id', 'parent_id', 'children');
// 输出:
// [
//     [
//         'id' => 1,
//         'name' => '部门1',
//         'parent_id' => 0,
//         'children' => [
//             [
//                 'id' => 2,
//                 'name' => '部门2',
//                 'parent_id' => 1,
//                 'children' => [
//                     ['id' => 4, 'name' => '部门4', 'parent_id' => 2, 'children' => []]
//                 ]
//             ],
//             [
//                 'id' => 3,
//                 'name' => '部门3',
//                 'parent_id' => 1,
//                 'children' => []
//             ]
//         ]
//     ]
// ]

#### list() - 树形结构转数组

// 树形结构转数组
$flat = Arr::list($tree, 'children');

#### level() - 数组转层级结构

// 数组转层级结构
$level = Arr::level($flatArray, 'id', 'parent_id', 'level');
// 输出:
// [
//     ['id' => 1, 'name' => '部门1', 'parent_id' => 0, 'level' => 1],
//     ['id' => 2, 'name' => '部门2', 'parent_id' => 1, 'level' => 2],
//     ['id' => 3, 'name' => '部门3', 'parent_id' => 1, 'level' => 2],
//     ['id' => 4, 'name' => '部门4', 'parent_id' => 2, 'level' => 3]
// ]

// 数组转路径结构
$path = Arr::path($flatArray, 'id', 'parent_id', 'name', 'path', '/');
// 输出:
// [
//     ['id' => 1, 'name' => '部门1', 'parent_id' => 0, 'path' => '部门1'],
//     ['id' => 2, 'name' => '部门2', 'parent_id' => 1, 'path' => '部门1/部门2'],
//     ['id' => 3, 'name' => '部门3', 'parent_id' => 1, 'path' => '部门1/部门3'],
//     ['id' => 4, 'name' => '部门4', 'parent_id' => 2, 'path' => '部门1/部门2/部门4']
// ]
```

### 数组访问和操作

#### deepMerge() - 数组深度合并

```php
use Kode\Array\Arr;

// 数组深度合并
$array1 = [
    'user' => [
        'name' => '张三',
        'age' => 25
    ],
    'settings' => [
        'theme' => 'dark'
    ]
];

$array2 = [
    'user' => [
        'age' => 26,
        'email' => 'user@example.com'
    ],
    'settings' => [
        'language' => 'zh-CN'
    ]
];

$merged = Arr::deepMerge($array1, $array2);
// 输出:
// [
//     'user' => [
//         'name' => '张三',
//         'age' => 26,
//         'email' => 'user@example.com'
//     ],
//     'settings' => [
//         'theme' => 'dark',
//         'language' => 'zh-CN'
//     ]
// ]

#### get() - 数组获取值（支持点语法）

// 数组获取值（支持点语法）
$value = Arr::get($array1, 'user.name'); // '张三'
$value = Arr::get($array1, ['user', 'age']); // 25
$value = Arr::get($array1, 'user.email', 'default@example.com'); // 'default@example.com'

#### set() - 数组设置值

// 数组设置值
$result = Arr::set($array1, 'user.age', 26);

#### has() - 数组判断是否存在键

// 数组判断是否存在键
$exists = Arr::has($array1, 'user.name'); // true
$exists = Arr::has($array1, 'user.email'); // false

#### only() - 数组仅保留指定键

// 数组仅保留指定键
$only = Arr::only($array1, ['user.name', 'user.age']);
// 输出: ['user' => ['name' => '张三', 'age' => 25]]

#### except() - 数组排除指定键

// 数组排除指定键
$except = Arr::except($array1, ['settings']);
// 输出: ['user' => ['name' => '张三', 'age' => 25]]
```

### 多维数组统计

#### group() - 多维数组分组

```php
use Kode\Array\Arr;

$flatArray = [
    ['id' => 1, 'name' => '部门1', 'parent_id' => 0],
    ['id' => 2, 'name' => '部门2', 'parent_id' => 1],
    ['id' => 3, 'name' => '部门3', 'parent_id' => 1],
    ['id' => 4, 'name' => '部门4', 'parent_id' => 2]
];

// 多维数组分组
$grouped = Arr::group($flatArray, 'parent_id');
// 输出:
// [
//     0 => [['id' => 1, 'name' => '部门1', 'parent_id' => 0]],
//     1 => [
//         ['id' => 2, 'name' => '部门2', 'parent_id' => 1],
//         ['id' => 3, 'name' => '部门3', 'parent_id' => 1]
//     ],
//     2 => [['id' => 4, 'name' => '部门4', 'parent_id' => 2]]
// ]

#### count() - 多维数组统计

// 多维数组统计
$count = Arr::count($flatArray, 'parent_id');
// 输出: [0 => 1, 1 => 2, 2 => 1]

#### sum() - 多维数组求和

// 多维数组求和
$sum = Arr::sum($flatArray, 'id'); // 10

#### avg() - 多维数组求平均值

// 多维数组求平均值
$avg = Arr::avg($flatArray, 'id'); // 2.5

#### max() - 多维数组求最大值

// 多维数组求最大值
$max = Arr::max($flatArray, 'id'); // 4

#### min() - 多维数组求最小值

// 多维数组求最小值
$min = Arr::min($flatArray, 'id'); // 1
```

### PHP 8.4+数组函数

#### first() - 数组首元素

```php
use Kode\Array\Arr;

// 数组首元素（PHP 8.4+使用原生array_first）
$first = Arr::first([1, 2, 3, 4, 5]); // 1
$first = Arr::first([]); // null

#### last() - 数组尾元素

// 数组尾元素（PHP 8.4+使用原生array_last）
$last = Arr::last([1, 2, 3, 4, 5]); // 5
$last = Arr::last([]); // null

#### find() - 查找满足条件的元素

// 查找满足条件的元素（PHP 8.4+使用原生array_find）
$found = Arr::find([1, 2, 3, 4, 5], fn($n) => $n > 2); // 3
$found = Arr::find([1, 2, 3, 4, 5], fn($n) => $n > 10); // null

#### findKey() - 查找满足条件的键名

// 查找满足条件的键名（PHP 8.4+使用原生array_find_key）
$foundKey = Arr::findKey(['a' => 1, 'b' => 2, 'c' => 3], fn($n) => $n > 1); // 'b'
$foundKey = Arr::findKey(['a' => 1, 'b' => 2, 'c' => 3], fn($n) => $n > 10); // null

#### any() - 检查是否存在满足条件的元素

// 检查是否存在满足条件的元素（PHP 8.4+使用原生array_any）
$hasAny = Arr::any([1, 2, 3, 4, 5], fn($n) => $n > 3); // true
$hasAny = Arr::any([1, 2, 3, 4, 5], fn($n) => $n > 10); // false

#### all() - 检查是否所有元素都满足条件

// 检查是否所有元素都满足条件（PHP 8.4+使用原生array_all）
$allMatch = Arr::all([1, 2, 3, 4, 5], fn($n) => $n > 0); // true
$allMatch = Arr::all([1, 2, 3, 4, 5], fn($n) => $n > 2); // false
```

### 数组查找和判断

```php
use Kode\Array\Arr;

// 数组映射
$mapped = Arr::map([1, 2, 3, 4, 5], fn($n) => $n * 2); // [2, 4, 6, 8, 10]

// 数组过滤
$filtered = Arr::filter([1, 2, 3, 4, 5], fn($n) => $n > 2); // [3, 4, 5]
$filtered = Arr::filter([1, 2, 0, null, false]); // [1, 2]（过滤空值）

// 数组归约
$sum = Arr::reduce([1, 2, 3, 4, 5], fn($carry, $n) => $carry + $n, 0); // 15

// 检查是否存在满足条件的元素（别名方法）
$hasSome = Arr::some([1, 2, 3, 4, 5], fn($n) => $n > 3); // true

// 检查是否所有元素都满足条件（别名方法）
$allMatch = Arr::every([1, 2, 3, 4, 5], fn($n) => $n > 0); // true

// 数组是否包含指定元素
$contains = Arr::contains([1, 2, 3, 4, 5], 3); // true
$contains = Arr::contains([1, 2, 3, 4, 5], '3', false); // true（非严格比较）
$contains = Arr::contains([1, 2, 3, 4, 5], '3', true); // false（严格比较）

// 数组是否包含指定键名
$containsKey = Arr::containsKey(['a' => 1, 'b' => 2], 'a'); // true

// 数组是否为空
$isEmpty = Arr::isEmpty([]); // true
$isEmpty = Arr::isEmpty([1, 2, 3]); // false

// 数组是否为关联数组
$isAssoc = Arr::isAssoc(['a' => 1, 'b' => 2]); // true
$isAssoc = Arr::isAssoc([1, 2, 3]); // false

// 数组是否为索引数组
$isIndexed = Arr::isIndexed([1, 2, 3]); // true
$isIndexed = Arr::isIndexed(['a' => 1, 'b' => 2]); // false
```

### 数组排序

```php
use Kode\Array\Arr;

$users = [
    ['id' => 3, 'name' => '张三', 'age' => 25],
    ['id' => 1, 'name' => '李四', 'age' => 30],
    ['id' => 2, 'name' => '王五', 'age' => 28]
];

// 数组排序（升序）
$sorted = Arr::sort($users, 'age', 'asc');
// 输出: [['id' => 3, 'name' => '张三', 'age' => 25], ['id' => 2, 'name' => '王五', 'age' => 28], ['id' => 1, 'name' => '李四', 'age' => 30]]

// 数组排序（降序）
$sorted = Arr::sort($users, 'age', 'desc');
// 输出: [['id' => 1, 'name' => '李四', 'age' => 30], ['id' => 2, 'name' => '王五', 'age' => 28], ['id' => 3, 'name' => '张三', 'age' => 25]]

// 多维数组排序
$multiSorted = Arr::multiSort($users, ['age', 'id'], ['asc', 'desc']);
// 先按age升序，age相同时按id降序

// 数组反转
$reversed = Arr::reverse([1, 2, 3, 4, 5]); // [5, 4, 3, 2, 1]
$reversed = Arr::reverse(['a' => 1, 'b' => 2], true); // ['b' => 2, 'a' => 1]（保留键名）

// 数组打乱
$shuffled = Arr::shuffle([1, 2, 3, 4, 5]); // 随机打乱顺序
```

### 数组去重

```php
use Kode\Array\Arr;

// 数组去重
$unique = Arr::unique([1, 2, 2, 3, 3, 3, 4, 4, 4, 4]); // [1, 2, 3, 4]
$unique = Arr::unique([1, '1', 2, '2'], true); // [1, '1', 2, '2]（严格比较）
$unique = Arr::unique([1, '1', 2, '2'], false); // [1, 2]（非严格比较）

// 多维数组去重
$users = [
    ['id' => 1, 'name' => '张三'],
    ['id' => 2, 'name' => '李四'],
    ['id' => 1, 'name' => '张三']
];
$unique = Arr::multiUnique($users, 'id');
// 输出: [['id' => 1, 'name' => '张三'], ['id' => 2, 'name' => '李四']]
```

### 数组分页和切片

```php
use Kode\Array\Arr;

// 数组分页
$array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
$page1 = Arr::paginate($array, 1, 5); // [1, 2, 3, 4, 5]
$page2 = Arr::paginate($array, 2, 5); // [6, 7, 8, 9, 10]
$page3 = Arr::paginate($array, 3, 5); // [11, 12]

// 数组切片
$sliced = Arr::slice([1, 2, 3, 4, 5], 1, 3); // [2, 3, 4]
$sliced = Arr::slice(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4], 1, 2, true); // ['b' => 2, 'c' => 3]（保留键名）

// 数组分割
$chunked = Arr::chunk([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 3);
// 输出: [[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]]
```

### 数组合并和差集

```php
use Kode\Array\Arr;

// 数组合并
$merged = Arr::merge([1, 2, 3], [4, 5, 6]); // [1, 2, 3, 4, 5, 6]

// 数组合并（保留键名）
$merged = Arr::mergeRecursive(['a' => 1], ['b' => 2]); // ['a' => 1, 'b' => 2]

// 数组差集
$diff = Arr::diff([1, 2, 3, 4, 5], [2, 4]); // [1, 3, 5]

// 数组差集（带键名）
$diff = Arr::diffKey(['a' => 1, 'b' => 2, 'c' => 3], ['b' => 2]); // ['a' => 1, 'c' => 3]

// 数组交集
$intersect = Arr::intersect([1, 2, 3, 4, 5], [2, 4, 6]); // [2, 4]

// 数组交集（带键名）
$intersect = Arr::intersectKey(['a' => 1, 'b' => 2, 'c' => 3], ['b' => 2, 'c' => 4]); // ['b' => 2, 'c' => 3]
```

### 数组转换

```php
use Kode\Array\Arr;

// 多维数组转JSON
$json = Arr::toJson(['name' => '张三', 'age' => 25]); // '{"name":"张三","age":25}'

// JSON转多维数组
$array = Arr::fromJson('{"name":"张三","age":25}'); // ['name' => '张三', 'age' => 25]

// 数组扁平化
$flattened = Arr::flatten([1, [2, [3, [4, 5]]]]); // [1, 2, 3, 4, 5]

// 数组键名
$keys = Arr::keys(['a' => 1, 'b' => 2, 'c' => 3]); // ['a', 'b', 'c']

// 数组值
$values = Arr::values(['a' => 1, 'b' => 2, 'c' => 3]); // [1, 2, 3]

// 数组翻转
$flipped = Arr::flip(['a' => 1, 'b' => 2, 'c' => 3]); // [1 => 'a', 2 => 'b', 3 => 'c']

// 数组列提取
$users = [
    ['id' => 1, 'name' => '张三', 'age' => 25],
    ['id' => 2, 'name' => '李四', 'age' => 30]
];
$names = Arr::column($users, 'name'); // ['张三', '李四']
$indexed = Arr::column($users, 'name', 'id'); // [1 => '张三', 2 => '李四']
```

### 数组键值操作

```php
use Kode\Array\Arr;

// 数组映射键名
$mapped = Arr::mapKeys(['a' => 1, 'b' => 2], fn($key) => strtoupper($key)); // ['A' => 1, 'B' => 2]

// 数组映射值
$mapped = Arr::mapValues(['a' => 1, 'b' => 2], fn($value) => $value * 2); // ['a' => 2, 'b' => 4]

// 数组合并键值
$combined = Arr::combine(['a', 'b', 'c'], [1, 2, 3]); // ['a' => 1, 'b' => 2, 'c' => 3]

// 数组填充键值
$filled = Arr::fillKeys(['a', 'b', 'c'], 0); // ['a' => 0, 'b' => 0, 'c' => 0]

// 数组填充
$filled = Arr::fill(0, 5, 0); // [0, 0, 0, 0, 0]
```

### 数组随机操作

```php
use Kode\Array\Arr;

// 数组随机元素
$random = Arr::random([1, 2, 3, 4, 5]); // 随机返回其中一个元素

// 数组随机多个元素
$randomMany = Arr::randomMany([1, 2, 3, 4, 5], 3); // 随机返回3个元素
```

### 数组元素操作

```php
use Kode\Array\Arr;

$array = [1, 2, 3, 4, 5];

// 数组弹出第一个元素
$first = Arr::shift($array); // 1，$array变为[2, 3, 4, 5]

// 数组弹出最后一个元素
$last = Arr::pop($array); // 5，$array变为[2, 3, 4]

// 数组头部添加元素
$count = Arr::unshift($array, 0); // 4，$array变为[0, 2, 3, 4]

// 数组尾部添加元素
$count = Arr::push($array, 5); // 5，$array变为[0, 2, 3, 4, 5]

// 数组删除指定元素
$removed = Arr::remove([1, 2, 3, 2, 4, 2], 2); // [1, 3, 4]

// 数组删除指定键名
$removed = Arr::removeKey(['a' => 1, 'b' => 2, 'c' => 3], 'b'); // ['a' => 1, 'c' => 3]
```

## 字符串处理模块使用示例

字符串处理模块提供了丰富的字符串操作功能，包括脱敏、验证、转换、编码解码等。

### 字符串脱敏

#### mask() - 字符串脱敏

```php
use Kode\String\Str;

// 字符串脱敏
$masked = Str::mask('13800138000', 3, 4, '*'); // '138****8000'
$masked = Str::mask('user@example.com', 2, 4, '*'); // 'us****@example.com'

#### maskPhone() - 手机号脱敏

// 手机号脱敏
$phone = Str::maskPhone('13800138000'); // '138****8000'
$phone = Str::maskPhone('13800138000', 3, 4); // '138****8000'

#### maskEmail() - 邮箱脱敏

// 邮箱脱敏
$email = Str::maskEmail('user@example.com'); // 'us***@example.com'
$email = Str::maskEmail('user@example.com', 2, 3); // 'us***@example.com'

#### maskIdCard() - 身份证号脱敏

// 身份证号脱敏
$idCard = Str::maskIdCard('110101199001011234'); // '110101********1234'
$idCard = Str::maskIdCard('110101199001011234', 6, 8); // '110101********1234'

#### maskBankCard() - 银行卡号脱敏

// 银行卡号脱敏
$bankCard = Str::maskBankCard('6222021234567890123'); // '622202********0123'
$bankCard = Str::maskBankCard('6222021234567890123', 6, 10); // '622202********0123'

#### maskName() - 姓名脱敏

// 姓名脱敏
$name = Str::maskName('张三'); // '张*'
$name = Str::maskName('欧阳修'); // '欧阳*'
$name = Str::maskName('张三丰', 1, 1); // '张*丰'
```

### 字符串长度和截断

#### length() - 字符串长度

```php
use Kode\String\Str;

// 字符串长度
$length = Str::length('你好世界'); // 4（使用mb_strlen）

#### truncate() - 字符串截断

// 字符串截断
$truncated = Str::truncate('这是一段很长的文本内容', 10); // '这是一段很长的文...'
$truncated = Str::truncate('这是一段很长的文本内容', 10, '---'); // '这是一段很长的文---'

#### limit() - 字符串限制长度

// 字符串限制长度
$limited = Str::limit('这是一段很长的文本内容', 10); // '这是一段很长的文...'
$limited = Str::limit('短文本', 10); // '短文本'（不超过长度不截断）

#### wordTruncate() - 字符串单词截断

// 字符串单词截断
$wordTruncate = Str::wordTruncate('This is a long text content', 10); // 'This is a...'
```

### 字符串命名转换

#### snake() - 驼峰转下划线

```php
use Kode\String\Str;

// 驼峰转下划线
$snake = Str::snake('helloWorld'); // 'hello_world'
$snake = Str::snake('HelloWorld'); // 'hello_world'
$snake = Str::snake('HelloWorld', '-'); // 'hello-world'

#### camel() - 下划线转驼峰

// 下划线转驼峰
$camel = Str::camel('hello_world'); // 'helloWorld'
$camel = Str::camel('hello-world'); // 'helloWorld'
$camel = Str::camel('hello_world', '-'); // 'helloWorld'

#### ucfirst() - 首字母大写

// 首字母大写
$ucfirst = Str::ucfirst('hello'); // 'Hello'

#### lcfirst() - 首字母小写

// 首字母小写
$lcfirst = Str::lcfirst('Hello'); // 'hello'

#### ucwords() - 单词首字母大写

// 单词首字母大写
$ucwords = Str::ucwords('hello world'); // 'Hello World'

#### upper() - 全部大写

// 全部大写
$upper = Str::upper('hello'); // 'HELLO'

#### lower() - 全部小写

// 全部小写
$lower = Str::lower('HELLO'); // 'hello'

#### swap() - 大小写转换

// 大小写转换
$swap = Str::swap('Hello'); // 'hELLO'

#### title() - 标题格式

// 标题格式
$title = Str::title('hello world'); // 'Hello World'
```

### 字符串查找和判断

```php
use Kode\String\Str;

// 是否包含子串
$contains = Str::contains('hello world', 'world'); // true
$contains = Str::contains('hello world', 'php'); // false

// 是否以子串开头
$startsWith = Str::startsWith('hello world', 'hello'); // true
$startsWith = Str::startsWith('hello world', 'world'); // false

// 是否以子串结尾
$endsWith = Str::endsWith('hello world', 'world'); // true
$endsWith = Str::endsWith('hello world', 'hello'); // false

// 子串首次出现位置
$pos = Str::pos('hello world', 'world'); // 6
$pos = Str::pos('hello world', 'php'); // false

// 子串最后一次出现位置
$pos = Str::rpos('hello world world', 'world'); // 12
$pos = Str::rpos('hello world', 'php'); // false

// 子串出现次数
$count = Str::count('hello world world', 'world'); // 2

// 字符串是否匹配正则
$match = Str::match('hello123', '/^[a-z]+\d+$/'); // true
$match = Str::match('hello', '/^[a-z]+\d+$/'); // false
```

### 字符串替换

```php
use Kode\String\Str;

// 字符串替换
$replaced = Str::replace('hello world', 'world', 'php'); // 'hello php'

// 字符串批量替换
$replaced = Str::replaceArray('hello world', ['hello' => 'hi', 'world' => 'php']); // 'hi php'

// 正则替换
$replaced = Str::replaceRegex('hello123world', '/\d+/', '456'); // 'hello456world'

// 字符串截取并替换
$replaced = Str::substrReplace('hello world', 'php', 6, 5); // 'hello php'
```

### 字符串分割和连接

```php
use Kode\String\Str;

// 字符串分割
$parts = Str::split('hello,world,php', ','); // ['hello', 'world', 'php']

// 字符串分割为数组
$array = Str::toArray('hello,world,php', ','); // ['hello', 'world', 'php']

// 数组连接为字符串
$string = Str::fromArray(['hello', 'world', 'php'], ','); // 'hello,world,php'

// 字符串连接
$joined = Str::join(['hello', 'world', 'php'], ','); // 'hello,world,php'

// 字符串拼接
$concat = Str::concat('hello', ' ', 'world'); // 'hello world'
```

### 字符串去除空白

```php
use Kode\String\Str;

// 去除首尾空白
$trimmed = Str::trim('  hello world  '); // 'hello world'

// 去除左侧空白
$ltrim = Str::ltrim('  hello world  '); // 'hello world  '

// 去除右侧空白
$rtrim = Str::rtrim('  hello world  '); // '  hello world'

// 去除所有空白
$clean = Str::clean('  hello   world  '); // 'helloworld'
```

### 字符串填充

```php
use Kode\String\Str;

// 左侧填充
$padded = Str::padLeft('123', 6, '0'); // '000123'

// 右侧填充
$padded = Str::padRight('123', 6, '0'); // '123000'

// 两侧填充
$padded = Str::padBoth('123', 7, '*'); // '**123**'
```

### 字符串重复

```php
use Kode\String\Str;

// 字符串重复
$repeated = Str::repeat('hello', 3); // 'hellohellohello'

// 字符串反转
$reversed = Str::reverse('hello'); // 'olleh'
```

### 字符串随机

```php
use Kode\String\Str;

// 生成随机字符串
$random = Str::random(16); // 16位随机字符串

// 生成随机数字字符串
$numeric = Str::numeric(6); // 6位随机数字字符串

// 生成随机字母字符串
$alpha = Str::alpha(8); // 8位随机字母字符串
```

### 字符串编码解码

#### toBase64() - Base64编码

```php
use Kode\String\Str;

// Base64编码
$base64 = Str::toBase64('hello'); // 'aGVsbG8='

#### fromBase64() - Base64解码

// Base64解码
$decoded = Str::fromBase64('aGVsbG8='); // 'hello'

#### toUrlEncode() - URL编码

// URL编码
$urlEncoded = Str::toUrlEncode('hello world'); // 'hello%20world'

#### fromUrlEncode() - URL解码

// URL解码
$urlDecoded = Str::fromUrlEncode('hello%20world'); // 'hello world'

#### toHtmlEntities() - HTML实体编码

// HTML实体编码
$htmlEncoded = Str::toHtmlEntities('<div>hello</div>'); // '&lt;div&gt;hello&lt;/div&gt;'

#### fromHtmlEntities() - HTML实体解码

// HTML实体解码
$htmlDecoded = Str::fromHtmlEntities('&lt;div&gt;hello&lt;/div&gt;'); // '<div>hello</div>'

#### toJson() - JSON编码

// JSON编码
$json = Str::toJson(['name' => '张三', 'age' => 25]); // '{"name":"张三","age":25}'

#### fromJson() - JSON解码

// JSON解码
$decoded = Str::fromJson('{"name":"张三","age":25}'); // ['name' => '张三', 'age' => 25]

#### toXml() - XML编码

// XML编码
$xml = Str::toXml(['name' => '张三', 'age' => 25], 'root'); // '<root><name>张三</name><age>25</age></root>'

#### fromXml() - XML解码

// XML解码
$decoded = Str::fromXml('<root><name>张三</name><age>25</age></root>'); // ['name' => '张三', 'age' => 25]

#### toBinary() - 二进制编码

// 二进制编码
$binary = Str::toBinary('hello'); // '0110100001100101011011000110110001101111'

#### fromBinary() - 二进制解码

// 二进制解码
$decoded = Str::fromBinary('0110100001100101011011000110110001101111'); // 'hello'

#### toHex() - 十六进制编码

// 十六进制编码
$hex = Str::toHex('hello'); // '68656c6c6f'

// 十六进制解码
$decoded = Str::fromHex('68656c6c6f'); // 'hello'
```

### 字符串哈希

```php
use Kode\String\Str;

// MD5哈希
$md5 = Str::md5('hello'); // '5d41402abc4b2a76b9719d911017c592'

// SHA1哈希
$sha1 = Str::sha1('hello'); // 'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d'

// SHA256哈希
$sha256 = Str::sha256('hello'); // '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'

// SHA512哈希
$sha512 = Str::sha512('hello'); // '9b71d224bd62f3785d96d46ad3ea3d73319bfbc2890caadae2dff72519673ca72323c3d99ba5c11d7c7acc6e14b8c5da0c4663475c2e5c3adef46f73bcdec043'
```

### 字符串验证

```php
use Kode\String\Str;

// 是否为邮箱
$isEmail = Str::isEmail('user@example.com'); // true
$isEmail = Str::isEmail('invalid-email'); // false

// 是否为URL
$isUrl = Str::isUrl('https://example.com'); // true
$isUrl = Str::isUrl('not-a-url'); // false

// 是否为IP地址
$isIp = Str::isIp('192.168.1.1'); // true
$isIp = Str::isIp('not-an-ip'); // false

// 是否为IPv4地址
$isIpv4 = Str::isIpv4('192.168.1.1'); // true
$isIpv4 = Str::isIpv4('2001:0db8:85a3:0000:0000:8a2e:0370:7334'); // false

// 是否为IPv6地址
$isIpv6 = Str::isIpv6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'); // true
$isIpv6 = Str::isIpv6('192.168.1.1'); // false

// 是否为手机号
$isPhone = Str::isPhone('13800138000'); // true
$isPhone = Str::isPhone('12345678901'); // false

// 是否为身份证号
$isIdCard = Str::isIdCard('110101199001011234'); // true
$isIdCard = Str::isIdCard('123456789012345678'); // false

// 是否为银行卡号
$isBankCard = Str::isBankCard('6222021234567890123'); // true
$isBankCard = Str::isBankCard('1234567890'); // false

// 是否为数字
$isNumeric = Str::isNumeric('12345'); // true
$isNumeric = Str::isNumeric('abc123'); // false

// 是否为字母
$isAlpha = Str::isAlpha('hello'); // true
$isAlpha = Str::isAlpha('hello123'); // false

// 是否为字母数字
$isAlnum = Str::isAlnum('hello123'); // true
$isAlnum = Str::isAlnum('hello world'); // false

// 是否为十六进制
$isHex = Str::isHex('1a2b3c'); // true
$isHex = Str::isHex('1g2h3i'); // false

// 是否为二进制
$isBinary = Str::isBinary('01010101'); // true
$isBinary = Str::isBinary('12345678'); // false

// 是否为JSON
$isJson = Str::isJson('{"name":"张三"}'); // true
$isJson = Str::isJson('not json'); // false

// 是否为XML
$isXml = Str::isXml('<root>hello</root>'); // true
$isXml = Str::isXml('not xml'); // false

// 是否为序列化数据
$isSerialized = Str::isSerialized('a:1:{i:0;s:5:"hello";}'); // true
$isSerialized = Str::isSerialized('not serialized'); // false

// 是否为Base64
$isBase64 = Str::isBase64('aGVsbG8='); // true
$isBase64 = Str::isBase64('not base64'); // false
```

### 字符串转换

```php
use Kode\String\Str;

// 字符串转数组
$array = Str::toArray('hello,world,php', ','); // ['hello', 'world', 'php']

// 数组转字符串
$string = Str::fromArray(['hello', 'world', 'php'], ','); // 'hello,world,php'

// 字符串转对象
$object = Str::toObject('{"name":"张三","age":25}'); // stdClass Object ( [name] => 张三 [age] => 25 )

// 对象转字符串
$string = Str::fromObject((object)['name' => '张三', 'age' => 25]); // '{"name":"张三","age":25}'

// 字符串转查询字符串
$query = Str::toQuery(['name' => '张三', 'age' => 25]); // 'name=%E5%BC%A0%E4%B8%89&age=25'

// 查询字符串转数组
$array = Str::fromQuery('name=%E5%BC%A0%E4%B8%89&age=25'); // ['name' => '张三', 'age' => 25]
```

### 字符串格式化

```php
use Kode\String\Str;

// 字符串格式化
$formatted = Str::format('Hello, %s! You are %d years old.', '张三', 25); // 'Hello, 张三! You are 25 years old.'

// 字符串模板渲染
$rendered = Str::template('Hello, {{name}}! Age: {{age}}', ['name' => '张三', 'age' => 25]); // 'Hello, 张三! Age: 25'

// 字符串缩进
$indented = Str::indent('hello', 4); // '    hello'

// 字符串去除缩进
$unindented = Str::unindent('    hello'); // 'hello'
```

### 字符串字符操作

```php
use Kode\String\Str;

// 获取字符串首字符
$first = Str::first('hello'); // 'h'

// 获取字符串尾字符
$last = Str::last('hello'); // 'o'

// 获取字符串前N个字符
$firstN = Str::firstN('hello', 2); // 'he'

// 获取字符串后N个字符
$lastN = Str::lastN('hello', 2); // 'lo'

// 去除首字符
$removed = Str::removeFirst('hello'); // 'ello'

// 去除尾字符
$removed = Str::removeLast('hello'); // 'hell'

// 去除前N个字符
$removed = Str::removeFirstN('hello', 2); // 'llo'

// 去除后N个字符
$removed = Str::removeLastN('hello', 2); // 'hel'
```

### 字符串统计

```php
use Kode\String\Str;

// 统计子串出现次数
$count = Str::count('hello world world', 'world'); // 2

// 统计单词数
$wordCount = Str::wordCount('hello world php'); // 3

// 统计字符数
$charCount = Str::charCount('hello'); // 5

// 统计字节长度
$byteLength = Str::byteLength('你好'); // 6（UTF-8编码）
```

### 字符串安全

```php
use Kode\String\Str;

// 转义HTML特殊字符
$escaped = Str::escapeHtml('<div>hello</div>'); // '&lt;div&gt;hello&lt;/div&gt;'

// 转义SQL特殊字符
$escaped = Str::escapeSql("O'Reilly"); // "O\'Reilly"

// 转义JavaScript特殊字符
$escaped = Str::escapeJs("It's a test"); // "It\'s a test"

// 转义正则表达式特殊字符
$escaped = Str::escapeRegex('hello.world'); // 'hello\.world'
```

### 字符串比较

```php
use Kode\String\Str;

// 字符串比较
$compare = Str::compare('hello', 'hello'); // 0（相等）
$compare = Str::compare('hello', 'world'); // -15（不相等）

// 字符串相似度
$similarity = Str::similarity('hello', 'hello'); // 1.0（完全相同）
$similarity = Str::similarity('hello', 'world'); // 0.2（相似度）

// 字符串编辑距离
$distance = Str::distance('hello', 'hello'); // 0（相同）
$distance = Str::distance('hello', 'world'); // 4（编辑距离）
```

## 时间处理模块使用示例

时间处理模块提供了丰富的时间操作功能，包括时间格式化、时间计算、日期范围计算、人性化时间显示等。

### 时间格式化

#### format() - 格式化时间

```php
use Kode\Time\Time;

// 格式化时间
$formatted = Time::format(time(), 'Y-m-d H:i:s'); // '2025-12-26 12:00:00'
$formatted = Time::format(time(), 'Y年m月d日'); // '2025年12月26日'
$formatted = Time::format(1735200000, 'Y-m-d'); // '2025-12-26'

#### now() - 获取当前时间

// 获取当前时间
$now = Time::now(); // '2025-12-26 12:00:00'
$now = Time::now('Y-m-d'); // '2025-12-26'
$now = Time::now('H:i:s'); // '12:00:00'

#### today() - 获取今天日期

// 获取今天日期
$today = Time::today(); // '2025-12-26'
$today = Time::today('Y-m-d H:i:s'); // '2025-12-26 00:00:00'

#### yesterday() - 获取昨天日期

// 获取昨天日期
$yesterday = Time::yesterday(); // '2025-12-25'
$yesterday = Time::yesterday('Y-m-d H:i:s'); // '2025-12-25 00:00:00'

#### tomorrow() - 获取明天日期

// 获取明天日期
$tomorrow = Time::tomorrow(); // '2025-12-27'
$tomorrow = Time::tomorrow('Y-m-d H:i:s'); // '2025-12-27 00:00:00'
```

### 时间计算

#### add() - 时间加法

```php
use Kode\Time\Time;

$timestamp = time();

// 时间加法
$newTime = Time::add($timestamp, 3600); // 加1小时
$newTime = Time::add($timestamp, 86400); // 加1天
$newTime = Time::add($timestamp, 604800); // 加1周

#### sub() - 时间减法

// 时间减法
$newTime = Time::sub($timestamp, 3600); // 减1小时
$newTime = Time::sub($timestamp, 86400); // 减1天
$newTime = Time::sub($timestamp, 604800); // 减1周

#### diff() - 时间差

// 时间差
$diff = Time::diff(time(), time() - 3600); // 3600（秒）
$diff = Time::diff(time() - 86400, time()); // 86400（秒）

#### diffForHumans() - 人性化时间差

// 人性化时间差
$human = Time::diffForHumans(time() - 60); // '1分钟前'
$human = Time::diffForHumans(time() - 3600); // '1小时前'
$human = Time::diffForHumans(time() - 86400); // '1天前'
$human = Time::diffForHumans(time() - 2592000); // '1个月前'
$human = Time::diffForHumans(time() - 31536000); // '1年前'
$human = Time::diffForHumans(time() + 3600); // '1小时后'
$human = Time::diffForHumans(time() + 86400); // '1天后'

// 指定基准时间的人性化时间差
$human = Time::diffForHumans(time() - 3600, time() - 7200); // '1小时前'
```

### 周日期范围

#### weekStart() - 获取本周开始时间

```php
use Kode\Time\Time;

// 获取本周开始时间
$weekStart = Time::weekStart(); // 本周一00:00:00的时间戳
$weekStart = Time::weekStart(time()); // 指定时间所在周的开始时间
$weekStartFormatted = Time::format(Time::weekStart(), 'Y-m-d H:i:s'); // '2025-12-22 00:00:00'

#### weekEnd() - 获取本周结束时间

// 获取本周结束时间
$weekEnd = Time::weekEnd(); // 本周日23:59:59的时间戳
$weekEnd = Time::weekEnd(time()); // 指定时间所在周的结束时间
$weekEndFormatted = Time::format(Time::weekEnd(), 'Y-m-d H:i:s'); // '2025-12-28 23:59:59'

#### lastWeekStart() - 获取上周开始时间

// 获取上周开始时间
$lastWeekStart = Time::lastWeekStart(); // 上周一00:00:00的时间戳
$lastWeekStartFormatted = Time::format(Time::lastWeekStart(), 'Y-m-d H:i:s'); // '2025-12-15 00:00:00'

#### lastWeekEnd() - 获取上周结束时间

// 获取上周结束时间
$lastWeekEnd = Time::lastWeekEnd(); // 上周日23:59:59的时间戳
$lastWeekEndFormatted = Time::format(Time::lastWeekEnd(), 'Y-m-d H:i:s'); // '2025-12-21 23:59:59'
```

### 月日期范围

#### monthStart() - 获取本月开始时间

```php
use Kode\Time\Time;

// 获取本月开始时间
$monthStart = Time::monthStart(); // 本月1日00:00:00的时间戳
$monthStart = Time::monthStart(time()); // 指定时间所在月的开始时间
$monthStartFormatted = Time::format(Time::monthStart(), 'Y-m-d H:i:s'); // '2025-12-01 00:00:00'

#### monthEnd() - 获取本月结束时间

// 获取本月结束时间
$monthEnd = Time::monthEnd(); // 本月最后一天23:59:59的时间戳
$monthEnd = Time::monthEnd(time()); // 指定时间所在月的结束时间
$monthEndFormatted = Time::format(Time::monthEnd(), 'Y-m-d H:i:s'); // '2025-12-31 23:59:59'

#### lastMonthStart() - 获取上月开始时间

// 获取上月开始时间
$lastMonthStart = Time::lastMonthStart(); // 上月1日00:00:00的时间戳
$lastMonthStartFormatted = Time::format(Time::lastMonthStart(), 'Y-m-d H:i:s'); // '2025-11-01 00:00:00'

#### lastMonthEnd() - 获取上月结束时间

// 获取上月结束时间
$lastMonthEnd = Time::lastMonthEnd(); // 上月最后一天23:59:59的时间戳
$lastMonthEndFormatted = Time::format(Time::lastMonthEnd(), 'Y-m-d H:i:s'); // '2025-11-30 23:59:59'
```

### 年日期范围

```php
use Kode\Time\Time;

// 获取本年开始时间
$yearStart = Time::yearStart(); // 本年1月1日00:00:00的时间戳
$yearStart = Time::yearStart(time()); // 指定时间所在年的开始时间
$yearStartFormatted = Time::format(Time::yearStart(), 'Y-m-d H:i:s'); // '2025-01-01 00:00:00'

// 获取本年结束时间
$yearEnd = Time::yearEnd(); // 本年12月31日23:59:59的时间戳
$yearEnd = Time::yearEnd(time()); // 指定时间所在年的结束时间
$yearEndFormatted = Time::format(Time::yearEnd(), 'Y-m-d H:i:s'); // '2025-12-31 23:59:59'

// 获取上年开始时间
$lastYearStart = Time::lastYearStart(); // 上年1月1日00:00:00的时间戳
$lastYearStartFormatted = Time::format(Time::lastYearStart(), 'Y-m-d H:i:s'); // '2024-01-01 00:00:00'

// 获取上年结束时间
$lastYearEnd = Time::lastYearEnd(); // 上年12月31日23:59:59的时间戳
$lastYearEndFormatted = Time::format(Time::lastYearEnd(), 'Y-m-d H:i:s'); // '2024-12-31 23:59:59'
```

### 时间判断

```php
use Kode\Time\Time;

$timestamp = time();

// 判断是否在某个时间区间内
$inRange = Time::between($timestamp, time() - 3600, time() + 3600); // true
$inRange = Time::between(time() - 7200, time() - 3600, time()); // false

// 判断是否是今天
$isToday = Time::isToday($timestamp); // true
$isToday = Time::isToday(time() - 86400); // false

// 判断是否是昨天
$isYesterday = Time::isYesterday(time() - 86400); // true
$isYesterday = Time::isYesterday(time()); // false

// 判断是否是明天
$isTomorrow = Time::isTomorrow(time() + 86400); // true
$isTomorrow = Time::isTomorrow(time()); // false

// 判断是否是本周
$isThisWeek = Time::isThisWeek($timestamp); // true
$isThisWeek = Time::isThisWeek(time() - 604800); // false

// 判断是否是本月
$isThisMonth = Time::isThisMonth($timestamp); // true
$isThisMonth = Time::isThisMonth(strtotime('2025-11-01')); // false

// 判断是否是本年
$isThisYear = Time::isThisYear($timestamp); // true
$isThisYear = Time::isThisYear(strtotime('2024-01-01')); // false
```

### 日期信息获取

```php
use Kode\Time\Time;

// 获取某个月的天数
$days = Time::daysInMonth(12); // 31（12月有31天）
$days = Time::daysInMonth(2, 2024); // 29（2024年2月有29天，闰年）
$days = Time::daysInMonth(2, 2023); // 28（2023年2月有28天，平年）

// 获取某天是周几
$weekday = Time::dayOfWeek(time()); // 0-6（0表示周日，1表示周一，以此类推）
$weekday = Time::dayOfWeek(strtotime('2025-12-26')); // 5（周五）

// 获取某天是周几（中文名称）
$weekdayName = Time::dayOfWeekName(time()); // '周五'
$weekdayName = Time::dayOfWeekName(strtotime('2025-12-28')); // '周日'

// 获取某天是本年第几天
$dayOfYear = Time::dayOfYear(time()); // 360（2025年第360天）
$dayOfYear = Time::dayOfYear(strtotime('2025-01-01')); // 1（第1天）

// 获取某天是本年第几周
$weekOfYear = Time::weekOfYear(time()); // 52（第52周）
$weekOfYear = Time::weekOfYear(strtotime('2025-01-01')); // 1（第1周）
```

### 年龄计算

```php
use Kode\Time\Time;

// 计算年龄
$age = Time::age('1990-01-01'); // 35（假设当前是2025年）
$age = Time::age('2000-12-31'); // 24（假设当前是2025年）
$age = Time::age('2010-06-15'); // 15（假设当前是2025年）
```

### 时间戳转换

```php
use Kode\Time\Time;

// 时间字符串转时间戳
$timestamp = Time::toTimestamp('2025-12-26 12:00:00'); // 1735200000
$timestamp = Time::toTimestamp('2025-12-26'); // 1735152000
$timestamp = Time::toTimestamp('now'); // 当前时间戳
$timestamp = Time::toTimestamp('+1 day'); // 明天同一时间的时间戳

// 时间戳转毫秒
$millisecond = Time::toMillisecond(time()); // 1735200000000
$millisecond = Time::toMillisecond(1735200000); // 1735200000000

// 毫秒转时间戳
$timestamp = Time::fromMillisecond(1735200000000); // 1735200000

// 获取当前毫秒时间戳
$millisecond = Time::millisecond(); // 当前时间的毫秒时间戳

// 获取当前微秒时间戳
$microsecond = Time::microsecond(); // 当前时间的微秒时间戳

// 获取当前时间戳（带微秒）
$microtime = Time::microtime(); // 1735200000.123456
```

### 时区操作

```php
use Kode\Time\Time;

// 获取当前时区
$timezone = Time::timezone(); // 'Asia/Shanghai'（或其他时区）

// 设置时区
$success = Time::setTimezone('UTC'); // true
$success = Time::setTimezone('America/New_York'); // true

// 设置时区后获取时间
Time::setTimezone('UTC');
$utcTime = Time::now(); // UTC时间

Time::setTimezone('Asia/Shanghai');
$shanghaiTime = Time::now(); // 上海时间
```

### 实际应用场景

```php
use Kode\Time\Time;

// 订单创建时间显示
$createdAt = time() - 3600; // 1小时前创建
$displayTime = Time::diffForHumans($createdAt); // '1小时前'

// 数据统计时间范围
$weekStart = Time::weekStart();
$weekEnd = Time::weekEnd();
// 查询本周数据：WHERE created_at >= $weekStart AND created_at <= $weekEnd

// 用户生日计算
$birthday = '1990-05-15';
$age = Time::age($birthday); // 35岁

// 活动倒计时
$endTime = strtotime('2025-12-31 23:59:59');
$diff = Time::diff(time(), $endTime); // 距离结束的秒数

// 日程安排
$today = Time::today();
$tomorrow = Time::tomorrow();
$weekStart = Time::weekStart();
$weekEnd = Time::weekEnd();

// 数据归档
$lastMonthStart = Time::lastMonthStart();
$lastMonthEnd = Time::lastMonthEnd();
// 归档上月数据：WHERE created_at >= $lastMonthStart AND created_at <= $lastMonthEnd

// 报表生成
$yearStart = Time::yearStart();
$yearEnd = Time::yearEnd();
// 生成本年报表：WHERE created_at >= $yearStart AND created_at <= $yearEnd

// 定时任务检查
if (Time::isToday($taskTime)) {
    // 执行今天的任务
}

// 工作日判断
$weekday = Time::dayOfWeek(time());
if ($weekday >= 1 && $weekday <= 5) {
    // 周一到周五，工作日
} else {
    // 周六周日，休息日
}

// 高精度时间测量
$startTime = Time::microsecond();
// 执行一些代码
$endTime = Time::microsecond();
$duration = $endTime - $startTime; // 微秒
```

## 加解密模块使用示例

#### cryptoMd5() - MD5加密（支持加盐）

```php
use Kode\Crypto\Crypto;

// MD5加密（支持加盐）
$md5 = Crypto::cryptoMd5('123456', 'salt123');

#### cryptoPasswordHash() - 密码哈希

// 密码哈希
$hash = Crypto::cryptoPasswordHash('123456');

#### cryptoPasswordVerify() - 密码验证

// 密码验证
$verify = Crypto::cryptoPasswordVerify('123456', $hash); // true

#### cryptoSslEncrypt() - SSL对称加密

// SSL对称加密
$key = '1234567890abcdef';
$encrypt = Crypto::cryptoSslEncrypt('敏感数据', $key);

#### cryptoSslDecrypt() - SSL对称解密

// SSL对称解密
$decrypt = Crypto::cryptoSslDecrypt($encrypt, $key); // '敏感数据'

#### cryptoHmac() - HMAC签名

// HMAC签名
$hmac = Crypto::cryptoHmac('数据', 'key', 'sha256');
```

## 代码生成模块使用示例

```php
use Kode\Generate\Generate;

// 订单号生成
$orderNo = Generate::orderNo(); // 202512251200001234

// 邀请码生成
$inviteCode = Generate::inviteCode(8); // 8位邀请码

// URL安全码生成
$urlSafe = Generate::urlSafeCode(16); // URL安全的随机码

// 注册码生成
$registerCode = Generate::registerCode(12, 4); // 12位注册码，每4位分隔
```

## 数学计算模块使用示例

### 基础运算

#### add() - 高精度加法

```php
use Kode\Math\Math;

// 高精度加法（解决浮点数精度丢失问题）
$sum = Math::add(0.1, 0.2); // 0.3（而不是0.30000000000000004）
$sum = Math::add('1.1', '2.2', 2); // 3.30（保留2位小数）

#### sub() - 高精度减法

// 高精度减法
$diff = Math::sub(0.3, 0.1); // 0.2
$diff = Math::sub('5.5', '2.2', 2); // 3.30

#### mul() - 高精度乘法

// 高精度乘法
$product = Math::mul(0.1, 0.2); // 0.02
$product = Math::mul('1.5', '2.5', 2); // 3.75

#### div() - 高精度除法

// 高精度除法
$quotient = Math::div(0.3, 0.1); // 3
$quotient = Math::div('10', '3', 2); // 3.33

#### mod() - 取模运算

// 取模运算
$mod = Math::mod(10, 3); // 1
$mod = Math::mod('10.5', '3'); // 1.5

#### pow() - 幂运算

// 幂运算
$pow = Math::pow(2, 10); // 1024
$pow = Math::pow('2.5', 3, 2); // 15.62

#### sqrt() - 平方根运算

// 平方根运算
$sqrt = Math::sqrt(16); // 4
$sqrt = Math::sqrt('2', 4); // 1.4142
```

### 取整运算

#### round() - 四舍五入

```php
// 四舍五入
$rounded = Math::round(3.14159, 2); // 3.14
$rounded = Math::round(3.5, 0); // 4

#### ceil() - 向上取整

// 向上取整
$ceil = Math::ceil(3.2); // 4
$ceil = Math::ceil(3.8, 1); // 3.8
$ceil = Math::ceil('3.21', 1); // 3.3

#### floor() - 向下取整

// 向下取整
$floor = Math::floor(3.8); // 3
$floor = Math::floor(3.2, 1); // 3.2
$floor = Math::floor('3.89', 1); // 3.8
```

### 比较运算

```php
// 比较两个数的大小
$result = Math::compare(5, 3); // 1（5 > 3）
$result = Math::compare(3, 5); // -1（3 < 5）
$result = Math::compare(5, 5); // 0（相等）

// 判断两个数是否相等
$equal = Math::equal(0.1 + 0.2, 0.3); // true（解决浮点数比较问题）
$equal = Math::equal('1.000', '1.00', 2); // true（保留2位小数比较）
```

### 格式化

```php
// 格式化数字
$formatted = Math::format(1234567.89, 2, true); // 1,234,567.89（带千分位）
$formatted = Math::format(1234567.89, 2, false); // 1234567.89（不带千分位）
$formatted = Math::format('1234567.89123', 3, true); // 1,234,567.891
```

### 三角函数

```php
// 正弦函数
$sin = Math::sin(Math::deg2rad(30)); // 0.5（30度的正弦值）
$sin = Math::sin(3.14159 / 2); // 1（π/2的正弦值）

// 余弦函数
$cos = Math::cos(Math::deg2rad(60)); // 0.5（60度的余弦值）
$cos = Math::cos(0); // 1（0度的余弦值）

// 正切函数
$tan = Math::tan(Math::deg2rad(45)); // 1（45度的正切值）
$tan = Math::tan(3.14159 / 4); // 1（π/4的正切值）

// 反正弦函数
$asin = Math::asin(1); // 1.5708（π/2）
$asin = Math::asin(0.5); // 0.5236（π/6）

// 反余弦函数
$acos = Math::acos(0); // 1.5708（π/2）
$acos = Math::acos(0.5); // 1.0472（π/3）

// 反正切函数
$atan = Math::atan(1); // 0.7854（π/4）
$atan = Math::atan(0); // 0
```

### 对数运算

```php
// 自然对数（以e为底）
$ln = Math::ln(Math::exp(1)); // 1
$ln = Math::ln(2.71828); // 1

// 常用对数（以10为底）
$log10 = Math::log10(100); // 2
$log10 = Math::log10(1000); // 3

// 自定义底数对数
$log = Math::log(8, 2); // 3（2的3次方等于8）
$log = Math::log(100, 10); // 2（10的2次方等于100）
```

### 角度转换

```php
// 弧度转角度
$deg = Math::rad2deg(3.14159); // 180（π弧度 = 180度）
$deg = Math::rad2deg(1.5708); // 90（π/2弧度 = 90度）

// 角度转弧度
$rad = Math::deg2rad(180); // 3.14159（180度 = π弧度）
$rad = Math::deg2rad(90); // 1.5708（90度 = π/2弧度）
```

### 数值操作

```php
// 绝对值
$abs = Math::abs(-10); // 10
$abs = Math::abs(10); // 10
$abs = Math::abs('-5.5'); // 5.5

// 阶乘
$factorial = Math::factorial(5); // 120（5! = 5×4×3×2×1）
$factorial = Math::factorial(0); // 1（0! = 1）

// 最大公约数
$gcd = Math::gcd(12, 18); // 6
$gcd = Math::gcd(24, 36); // 12

// 最小公倍数
$lcm = Math::lcm(4, 6); // 12
$lcm = Math::lcm(3, 5); // 15
```

### 金融计算

#### percentage() - 百分比计算

```php
// 百分比计算
$percentage = Math::percentage(25, 100); // 25（25占100的25%）
$percentage = Math::percentage('30', '150', 2); // 20.00

#### discount() - 折扣计算

// 折扣计算
$discounted = Math::discount(100, 0.8); // 80（打8折）
$discounted = Math::discount('200', '0.7', 2); // 140.00（打7折）

#### tax() - 税费计算

// 税费计算
$tax = Math::tax(100, 0.1); // 10（100的10%税额）
$tax = Math::tax('500', '0.13', 2); // 65.00（500的13%税额）

#### taxIncluded() - 含税金额计算

// 含税金额计算
$taxIncluded = Math::taxIncluded(100, 0.1); // 110（100 + 10%税）
$taxIncluded = Math::taxIncluded('500', '0.13', 2); // 565.00

#### taxExcluded() - 不含税金额计算

// 不含税金额计算
$taxExcluded = Math::taxExcluded(110, 0.1); // 100（110 / 1.1）
$taxExcluded = Math::taxExcluded('565', '0.13', 2); // 500.00

#### simpleInterest() - 简单利息计算

// 简单利息计算
$simpleInterest = Math::simpleInterest(1000, 0.05, 2); // 100（1000本金，5%年利率，2年）
$simpleInterest = Math::simpleInterest('5000', '0.04', 3, 2); // 600.00

#### compoundInterest() - 复利计算

// 复利计算
$compoundInterest = Math::compoundInterest(1000, 0.05, 2); // 1102.5（1000本金，5%年利率，2年复利）
$compoundInterest = Math::compoundInterest('5000', '0.04', 3, 2); // 5624.32
```

### 随机数生成

```php
// 生成指定范围内的随机数
$random = Math::random(1, 100); // 1-100之间的随机整数
$random = Math::random('1.5', '5.5', 2); // 1.50-5.50之间的随机数（保留2位小数）
```

### 范围检查

```php
// 数值范围检查
$inRange = Math::inRange(5, 1, 10); // true（5在1-10范围内）
$inRange = Math::inRange(15, 1, 10); // false（15不在1-10范围内）

// 限制数值范围
$clamped = Math::clamp(5, 1, 10); // 5（在范围内，保持不变）
$clamped = Math::clamp(15, 1, 10); // 10（超出最大值，限制为10）
$clamped = Math::clamp(-5, 1, 10); // 1（小于最小值，限制为1）
$clamped = Math::clamp('5.5', '1.0', '10.0', 1); // 5.5
```

### 数值判断

```php
// 判断是否为正数
$isPositive = Math::isPositive(10); // true
$isPositive = Math::isPositive(-5); // false
$isPositive = Math::isPositive(0); // false

// 判断是否为负数
$isNegative = Math::isNegative(-5); // true
$isNegative = Math::isNegative(10); // false
$isNegative = Math::isNegative(0); // false

// 判断是否为零
$isZero = Math::isZero(0); // true
$isZero = Math::isZero(0.0000000001, 10); // true（保留10位小数比较）
$isZero = Math::isZero(1); // false

// 判断是否为偶数
$isEven = Math::isEven(4); // true
$isEven = Math::isEven(5); // false

// 判断是否为奇数
$isOdd = Math::isOdd(5); // true
$isOdd = Math::isOdd(4); // false

// 判断是否为质数
$isPrime = Math::isPrime(7); // true
$isPrime = Math::isPrime(4); // false
$isPrime = Math::isPrime(2); // true
$isPrime = Math::isPrime(1); // false

// 判断数值是否有效
$isValid = Math::isValid(123); // true
$isValid = Math::isValid('123.45'); // true
$isValid = Math::isValid('abc'); // false
$isValid = Math::isValid(INF); // false（无穷大）
$isValid = Math::isValid(NAN); // false（非数字）
```

### 插值运算

```php
// 线性插值
$lerp = Math::lerp(0, 10, 0.5); // 5（0和10的中点）
$lerp = Math::lerp(0, 10, 0.25); // 2.5（0和10的25%位置）
$lerp = Math::lerp('0', '100', '0.75', 1); // 75.0
```

### 统计分析

```php
// 平均值计算
$avg = Math::average([1, 2, 3, 4, 5]); // 3
$avg = Math::average([1.5, 2.5, 3.5], 2); // 2.50

// 中位数计算
$median = Math::median([1, 2, 3, 4, 5]); // 3
$median = Math::median([1, 2, 3, 4]); // 2.5（2和3的平均值）
$median = Math::median(['1.5', '2.5', '3.5'], 2); // 2.50

// 众数计算
$mode = Math::mode([1, 2, 2, 3, 3, 3]); // 3（出现次数最多）
$mode = Math::mode([1, 2, 3]); // 1（多个众数时返回第一个）

// 标准差计算
$stdDev = Math::standardDeviation([1, 2, 3, 4, 5]); // 1.414
$stdDev = Math::standardDeviation([10, 20, 30], 2); // 10.00
```

### 实际应用场景

```php
// 电商订单金额计算
$subtotal = Math::add('99.99', '49.99'); // 149.98（商品小计）
$discount = Math::discount($subtotal, '0.9'); // 134.98（打9折）
$tax = Math::tax($discount, '0.13'); // 17.55（13%税）
$total = Math::add($discount, $tax, 2); // 152.53（总金额）

// 贷款利息计算
$principal = 100000; // 本金10万
$rate = 0.05; // 年利率5%
$years = 5; // 5年
$simpleInterest = Math::simpleInterest($principal, $rate, $years); // 25000（简单利息）
$compoundInterest = Math::compoundInterest($principal, $rate, $years); // 27628.16（复利）

// 数据分析
$scores = [85, 92, 78, 90, 88, 95, 82];
$avg = Math::average($scores, 2); // 87.14（平均分）
$median = Math::median($scores, 2); // 88.00（中位数）
$stdDev = Math::standardDeviation($scores, 2); // 5.67（标准差）

// 价格范围检查
$price = 99.99;
$minPrice = 50;
$maxPrice = 100;
if (Math::inRange($price, $minPrice, $maxPrice)) {
    echo '价格在合理范围内';
}

// 数值格式化显示
$amount = 1234567.89123;
$formatted = Math::format($amount, 2, true); // 1,234,567.89
```

## 地理位置模块使用示例

#### distance() - 计算两个坐标之间的距离

```php
use Kode\Geo\Geo;

// 计算两个坐标之间的距离
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'km'); // 北京到上海的距离，约1067公里

#### isValidCoordinate() - 坐标验证

// 坐标验证
$valid = Geo::isValidCoordinate(39.9042, 116.4074); // true

#### wgs84ToGcj02() - 坐标转换（WGS84转GCJ02）

// 坐标转换（WGS84转GCJ02）
$gcj02 = Geo::wgs84ToGcj02(39.9042, 116.4074);

#### gcj02ToBd09() - 坐标转换（GCJ02转BD09）

// 坐标转换（GCJ02转BD09）
$bd09 = Geo::gcj02ToBd09($gcj02[0], $gcj02[1]);
```

## IP地址模块使用示例

```php
use Kode\Ip\Ip;

// 获取真实客户端IP
$ip = Ip::getRealIp(); // 获取真实IP地址

// IP地址验证
$valid = Ip::isValid('192.168.1.1'); // true

// 私有IP地址判断
$private = Ip::isPrivate('192.168.1.1'); // true

// IP地址转换
$long = Ip::toLong('192.168.1.1'); // 3232235777
$ipStr = Ip::toString(3232235777); // '192.168.1.1'
```

## 消息体模块使用示例

消息体模块提供了完整的API响应构建能力，支持双模式链式调用（实例链式 + 静态链式）、灵活的状态码管理、自定义字段扩展和字段映射功能。

### 基础使用示例

#### code() - 设置状态码

```php
use Kode\Message\Message;

// 实例链式调用 - 构建标准响应
$msg = new Message();
$res = $msg->code(200)
           ->msg('操作成功')
           ->data(['id' => 123, 'name' => '测试用户'])
           ->page(['page' => 1, 'size' => 20, 'total' => 100])
           ->ext('trace_id', 'abc123')
           ->ext('request_id', 'req_20250726123456')
           ->result();

// 输出结果
// [
//     'code' => 200,
//     'msg' => '操作成功',
//     'data' => ['id' => 123, 'name' => '测试用户'],
//     'page' => ['page' => 1, 'size' => 20, 'total' => 100],
//     'trace_id' => 'abc123',
//     'request_id' => 'req_20250726123456'
// ]

// 静态链式调用 - 快速构建响应
$res = Message::code(300000)
              ->msg('Token失效，请重新登录')
              ->result();

// 输出结果
// [
//     'code' => 300000,
//     'msg' => 'Token失效，请重新登录'
// ]

// 构造函数初始化 - 一步到位
$res = new Message(200, '创建成功', ['id' => 456]);
// 输出结果
// [
//     'code' => 200,
//     'msg' => '创建成功',
//     'data' => ['id' => 456]
// ]
```

### 状态码管理

消息体模块内置了完整的状态码映射体系，支持基础HTTP状态码（200/400/500）和6位业务自定义码。

```php
use Kode\Message\Message;

// 基础成功状态码（200系列）
$res = Message::code(200)->msg('请求成功')->result();
// ['code' => 200, 'msg' => '请求成功']

$res = Message::code(201)->msg('资源创建成功')->result();
// ['code' => 201, 'msg' => '资源创建成功']

$res = Message::code(204)->msg('删除成功')->result();
// ['code' => 204, 'msg' => '删除成功']

// 基础客户端错误状态码（400系列）
$res = Message::code(400)->msg('请求参数错误')->result();
// ['code' => 400, 'msg' => '请求参数错误']

$res = Message::code(401)->msg('未授权访问')->result();
// ['code' => 401, 'msg' => '未授权访问']

$res = Message::code(403)->msg('禁止访问')->result();
// ['code' => 403, 'msg' => '禁止访问']

$res = Message::code(404)->msg('资源不存在')->result();
// ['code' => 404, 'msg' => '资源不存在']

$res = Message::code(422)->msg('数据验证失败')->result();
// ['code' => 422, 'msg' => '数据验证失败']

// 基础服务器错误状态码（500系列）
$res = Message::code(500)->msg('服务器内部错误')->result();
// ['code' => 500, 'msg' => '服务器内部错误']

$res = Message::code(502)->msg('网关错误')->result();
// ['code' => 502, 'msg' => '网关错误']

$res = Message::code(503)->msg('服务不可用')->result();
// ['code' => 503, 'msg' => '服务不可用']

// 6位业务自定义码（300000-399999为业务级错误）
$res = Message::code(300000)->msg('Token失效')->result();
// ['code' => 300000, 'msg' => 'Token失效']

$res = Message::code(300001)->msg('Token已过期')->result();
// ['code' => 300001, 'msg' => 'Token已过期']

$res = Message::code(300002)->msg('Token签名无效')->result();
// ['code' => 300002, 'msg' => 'Token签名无效']

// 4位业务自定义码（400000-499999为业务级错误）
$res = Message::code(400001)->msg('用户不存在')->result();
// ['code' => 400001, 'msg' => '用户不存在']

$res = Message::code(400002)->msg('用户已被禁用')->result();
// ['code' => 400002, 'msg' => '用户已被禁用']

$res = Message::code(400003)->msg('密码错误')->result();
// ['code' => 400003, 'msg' => '密码错误']

// 5位业务自定义码（500000-599999为系统级错误）
$res = Message::code(500001)->msg('数据库连接失败')->result();
// ['code' => 500001, 'msg' => '数据库连接失败']

$res = Message::code(500002)->msg('缓存服务不可用')->result();
// ['code' => 500002, 'msg' => '缓存服务不可用']
```

### 自定义状态码映射

#### setCodeMap() - 全局设置状态码映射

用户可以动态覆盖或新增状态码映射，满足业务特定需求。

```php
use Kode\Message\Message;

// 方式一：全局设置状态码映射
Message::setCodeMap([
    800000 => '自定义业务异常',
    800001 => '订单已取消',
    800002 => '订单已完成',
    900000 => '权限不足',
    900001 => '无操作权限',
    900002 => '角色受限'
]);

// 使用自定义状态码
$res = Message::code(800000)->result();
// ['code' => 800000, 'msg' => '自定义业务异常']

$res = Message::code(800001)->result();
// ['code' => 800001, 'msg' => '订单已取消']

$res = Message::code(900000)->result();
// ['code' => 900000, 'msg' => '权限不足']

#### setCustomCodes() - 实例级设置状态码映射

// 方式二：实例级设置状态码映射
$msg = new Message();
$msg->setCustomCodes([
    700001 => '商品库存不足',
    700002 => '商品已下架',
    700003 => '优惠券已过期'
]);

$res = $msg->code(700001)->result();
// ['code' => 700001, 'msg' => '商品库存不足']

#### addCode() - 动态添加单个状态码

// 方式三：动态添加单个状态码
$msg = new Message();
$msg->addCode(600001, '验证码错误')
    ->addCode(600002, '验证码已过期')
    ->addCode(600003, '发送频率过快');

// 使用动态添加的状态码
$res = $msg->code(600001)->result();
// ['code' => 600001, 'msg' => '验证码错误']

#### getCodeMsg() - 获取状态码对应的消息

// 获取状态码对应的消息
$msg = new Message();
$message = $msg->getCodeMsg(300000); // 'Token失效'
$message = $msg->getCodeMsg(400001); // '用户不存在'
$message = $msg->getCodeMsg(999999); // null（未定义的状态码）

#### codeExists() - 检查状态码是否存在

// 检查状态码是否存在
$exists = $msg->codeExists(200); // true
$exists = $msg->codeExists(300000); // true
$exists = $msg->codeExists(999999); // false
```

### 动态字段扩展

#### 动态字段扩展

消息体模块支持任意动态字段扩展，通过`__call`魔术方法实现灵活的属性设置。

```php
use Kode\Message\Message;

// 基础动态字段扩展
$msg = new Message(200, 'success');
$res = $msg->page(['page' => 1, 'size' => 10, 'total' => 100])
           ->list(['id' => 1, 'name' => 'Item 1'])
           ->total(100)
           ->page_num(1)
           ->page_size(10)
           ->result();

// 输出结果
// [
//     'code' => 200,
//     'msg' => 'success',
//     'page' => ['page' => 1, 'size' => 10, 'total' => 100],
//     'list' => ['id' => 1, 'name' => 'Item 1'],
//     'total' => 100,
//     'page_num' => 1,
//     'page_size' => 10
// ]

#### addFields() - 批量添加扩展字段

// 批量添加扩展字段
$msg = new Message(200, 'success');
$res = $msg->addFields([
        'created_at' => '2025-12-26 12:00:00',
        'updated_at' => '2025-12-26 12:30:00',
        'operator' => 'admin',
        'source' => 'web'
    ])
    ->result();

// 输出结果
// [
//     'code' => 200,
//     'msg' => 'success',
//     'created_at' => '2025-12-26 12:00:00',
//     'updated_at' => '2025-12-26 12:30:00',
//     'operator' => 'admin',
//     'source' => 'web'
// ]

#### 复杂数据结构扩展

// 复杂数据结构扩展
$msg = new Message(200, 'success');
$res = $msg->user_info([
        'id' => 10001,
        'name' => '张三',
        'avatar' => 'https://example.com/avatar.png',
        'level' => 5
    ])
    ->permissions(['read', 'write', 'delete'])
    ->roles(['admin', 'editor'])
    ->metadata([
        'browser' => 'Chrome',
        'platform' => 'Windows',
        'ip' => '192.168.1.100'
    ])
    ->result();

// 输出结果
// [
//     'code' => 200,
//     'msg' => 'success',
//     'user_info' => ['id' => 10001, 'name' => '张三', ...],
//     'permissions' => ['read', 'write', 'delete'],
//     'roles' => ['admin', 'editor'],
//     'metadata' => ['browser' => 'Chrome', ...]
// ]
```

### 字段映射与转换

#### setGlobalFieldMap() - 全局字段映射设置

支持自定义字段映射，满足不同项目或前端框架的命名规范。

```php
use Kode\Message\Message;

// 全局字段映射设置
Message::setGlobalFieldMap([
    'code' => 'status',
    'msg' => 'message',
    'data' => 'payload',
    'page' => 'pagination'
]);

// 使用全局字段映射
$res = Message::code(200)
              ->msg('操作成功')
              ->data(['id' => 123])
              ->page(['page' => 1])
              ->result();

// 输出结果（使用全局映射）
// [
//     'status' => 200,
//     'message' => '操作成功',
//     'payload' => ['id' => 123],
//     'pagination' => ['page' => 1]
// ]

#### fieldMap() - 实例级字段映射

// 实例级字段映射（覆盖全局设置）
$msg = new Message();
$res = $msg->fieldMap([
        'code' => 'errcode',
        'msg' => 'errmsg',
        'data' => 'result'
    ])
    ->code(200)
    ->msg('成功')
    ->data(['list' => [1, 2, 3]])
    ->result();

// 输出结果（使用实例级映射）
// [
//     'errcode' => 200,
//     'errmsg' => '成功',
//     'result' => ['list' => [1, 2, 3]]
// ]

#### 局部字段映射

// 局部字段映射（在result方法中传入）
$msg = new Message(200, 'success');
$res = $msg->data(['id' => 123])
           ->ext('trace_id', 'abc123')
           ->result(['code' => 'status', 'msg' => 'info']);

// 输出结果（使用局部映射）
// [
//     'status' => 200,
//     'info' => 'success',
//     'data' => ['id' => 123],
//     'trace_id' => 'abc123'
// ]
```

### JSON输出

#### json() - 基本JSON输出

支持直接输出JSON格式响应。

```php
use Kode\Message\Message;

// 基本JSON输出
$msg = new Message();
$json = $msg->code(200)
            ->msg('操作成功')
            ->data(['id' => 123, 'name' => '测试'])
            ->json();

echo $json;
// 输出: {"code":200,"msg":"操作成功","data":{"id":123,"name":"测试"}}

// 静态方法JSON输出
$json = Message::code(300000)
               ->msg('Token失效')
               ->json();

echo $json;
// 输出: {"code":300000,"msg":"Token失效"}

// 带字段映射的JSON输出
$msg = new Message();
$json = $msg->code(200)
            ->msg('成功')
            ->data(['items' => [1, 2, 3]])
            ->json(['code' => 'status', 'msg' => 'message']);

echo $json;
// 输出: {"status":200,"message":"成功","items":[1,2,3]}

// 字符串转换（__toString）
$msg = new Message();
$str = (string)$msg->code(200)->msg('成功')->data(['id' => 1]);
echo $str;
// 输出: {"code":200,"msg":"成功","data":{"id":1}}
```

### 实际业务场景示例

```php
use Kode\Message\Message;

// 场景一：用户登录接口响应
function loginResponse(bool $success, array $userInfo = [], string $token = ''): array
{
    if ($success) {
        return Message::code(200)
                      ->msg('登录成功')
                      ->data([
                          'user' => $userInfo,
                          'token' => $token
                      ])
                      ->result();
    }
    
    return Message::code(400001)
                  ->msg('用户名或密码错误')
                  ->result();
}

// 场景二：分页列表接口响应
function listResponse(array $list, int $page, int $size, int $total): array
{
    return Message::code(200)
                  ->msg('获取成功')
                  ->data(['list' => $list])
                  ->page([
                      'page' => $page,
                      'size' => $size,
                      'total' => $total,
                      'pages' => ceil($total / $size)
                  ])
                  ->result();
}

// 场景三：表单验证失败响应
function validationResponse(array $errors): array
{
    $errorMessages = [];
    foreach ($errors as $field => $message) {
        $errorMessages[] = "{$field}: {$message}";
    }
    
    return Message::code(400)
                  ->msg('参数验证失败')
                  ->data(['errors' => $errors])
                  ->ext('error_count', count($errors))
                  ->ext('error_details', implode('; ', $errorMessages))
                  ->result();
}

// 场景四：异常处理响应
function errorResponse(int $code, string $message, ?string $traceId = null): array
{
    $msg = Message::code($code)->msg($message);
    
    if ($traceId) {
        $msg->ext('trace_id', $traceId);
    }
    
    return $msg->result();
}

// 场景五：创建资源成功响应
function createdResponse(mixed $resource, string $location = ''): array
{
    $msg = Message::code(201)
                  ->msg('资源创建成功')
                  ->data(['resource' => $resource]);
    
    if ($location) {
        $msg->ext('location', $location);
    }
    
    return $msg->result();
}

// 场景六：批量操作响应
function batchResponse(int $success, int $failed, array $details = []): array
{
    $code = $failed > 0 ? 400 : 200;
    $msg = $failed > 0 ? '部分操作失败' : '全部操作成功';
    
    return Message::code($code)
                  ->msg($msg)
                  ->data([
                      'success' => $success,
                      'failed' => $failed,
                      'total' => $success + $failed
                  ])
                  ->ext('success_rate', round($success / ($success + $failed) * 100, 2))
                  ->ext('details', $details)
                  ->result();
}

// 场景七：文件上传响应
function uploadResponse(string $fileUrl, string $fileName, int $fileSize): array
{
    return Message::code(200)
                  ->msg('文件上传成功')
                  ->data([
                      'url' => $fileUrl,
                      'filename' => $fileName,
                      'size' => $fileSize
                  ])
                  ->ext('domain', parse_url($fileUrl, PHP_URL_HOST))
                  ->result();
}

// 场景八：搜索结果响应
function searchResponse(array $results, string $keyword, int $total): array
{
    return Message::code(200)
                  ->msg('搜索完成')
                  ->data(['results' => $results])
                  ->ext('keyword', $keyword)
                  ->ext('total', $total)
                  ->ext('highlight', true)
                  ->result();
}

// 场景九：认证失败响应（统一认证错误码）
function authFailedResponse(string $reason = '未授权'): array
{
    $errorCodes = [
        '未授权' => 401,
        'Token失效' => 300000,
        'Token过期' => 300001,
        '权限不足' => 900000,
        '账户禁用' => 400002
    ];
    
    $code = $errorCodes[$reason] ?? 401;
    
    return Message::code($code)
                  ->msg($reason)
                  ->ext('reason', $reason)
                  ->ext('need_relogin', true)
                  ->result();
}

// 场景十：服务降级响应
function degradedResponse(string $service, string $message): array
{
    return Message::code(503)
                  ->msg($message)
                  ->ext('service', $service)
                  ->ext('degraded_at', date('Y-m-d H:i:s'))
                  ->ext('retry_after', 30)
                  ->result();
}
```

### 构造函数初始化

支持通过构造函数一步完成初始化，简化代码。

```php
use Kode\Message\Message;

// 基础初始化
$msg1 = new Message();
$res1 = $msg1->result();
// ['code' => 200, 'msg' => 'success']

// 指定状态码
$msg2 = new Message(400);
$res2 = $msg2->result();
// ['code' => 400, 'msg' => '请求参数错误']

// 指定状态码和消息
$msg3 = new Message(200, '查询成功');
$res3 = $msg3->result();
// ['code' => 200, 'msg' => '查询成功']

// 指定状态码、消息和数据
$msg4 = new Message(201, '创建成功', ['id' => 100, 'name' => '新项目']);
$res4 = $msg4->result();
// ['code' => 201, 'msg' => '创建成功', 'data' => ['id' => 100, 'name' => '新项目']]

// 静态方法初始化（简化版）
$res5 = (new Message(200, '成功', ['data' => 123]))->result();
// ['code' => 200, 'msg' => '成功', 'data' => ['data' => 123]]
```

### 错误处理与异常捕获

```php
use Kode\Message\Message;

// 基础响应构建（无异常）
try {
    $msg = new Message();
    $result = $msg->code(200)
                  ->msg('操作成功')
                  ->data(['id' => 1])
                  ->result();
    
    echo json_encode($result);
} catch (\Exception $e) {
    // 捕获异常，返回错误响应
    $errorResponse = Message::code(500)
                            ->msg('系统异常')
                            ->ext('error', $e->getMessage())
                            ->result();
    
    http_response_code(500);
    echo json_encode($errorResponse);
}

// 自定义状态码文件加载
try {
    // 加载自定义状态码配置文件
    $msg = new Message(200, 'success', null, '/path/to/custom_codes.php');
    $result = $msg->code(800001)->result();
} catch (\Exception $e) {
    // 配置文件加载失败
    $msg = new Message();
    $result = $msg->code(500)
                  ->msg('状态码配置加载失败')
                  ->ext('error', $e->getMessage())
                  ->result();
}

// 重新加载自定义状态码文件
$msg = new Message(200, 'success', null, '/path/to/codes.php');
// ... 使用过程中的代码修改 ...

// 重新加载配置
$msg->reloadCustomCodeFile();

// 使用重新加载后的配置
$result = $msg->code(800001)->result();
```

## HTTP请求模块使用示例

HTTP请求模块提供了完整的HTTP客户端功能，支持多种HTTP方法、请求配置、响应处理、错误处理、并发请求和PHP 8.5+持久化句柄支持。

### 基础使用示例

#### get() - GET请求

```php
use Kode\Curl\Curl;

// GET请求
$response = Curl::get('https://api.example.com/users');
echo $response->body();
echo $response->json('data');

#### post() - POST请求

// POST请求（JSON）
$response = Curl::post('https://api.example.com/users', [
    'name' => '张三',
    'email' => 'zhangsan@example.com'
]);
echo $response->json('id');

// POST请求（表单）
$response = Curl::post('https://api.example.com/login', [
    'username' => 'admin',
    'password' => '123456'
], 'form');

// POST请求（multipart）
$response = Curl::post('https://api.example.com/upload', [
    'file' => new \CURLFile('/path/to/file.jpg')
], 'multipart');
```

### 多种HTTP方法

#### put() - PUT请求

```php
use Kode\Curl\Curl;

// PUT请求
$response = Curl::put('https://api.example.com/users/1', [
    'name' => '李四'
]);
echo $response->json();

#### patch() - PATCH请求

// PATCH请求
$response = Curl::patch('https://api.example.com/users/1', [
    'status' => 'active'
]);
echo $response->json();

#### delete() - DELETE请求

// DELETE请求
$response = Curl::delete('https://api.example.com/users/1');
echo $response->json();

#### head() - HEAD请求

// HEAD请求（仅获取响应头）
$response = Curl::head('https://api.example.com/users');
echo $response->header('Content-Type');

#### options() - OPTIONS请求

// OPTIONS请求
$response = Curl::options('https://api.example.com/users');
echo $response->header('Allow');
```

### 请求配置选项

#### 请求头设置

```php
use Kode\Curl\Curl;

// 设置请求头
$response = Curl::get('https://api.example.com/users', [
    'Authorization' => 'Bearer token123',
    'Accept-Language' => 'zh-CN'
]);
echo $response->json();

#### 超时时间设置

// 设置超时时间
$response = Curl::get('https://api.example.com/users', timeout: 30);
echo $response->json();

#### 用户代理设置

// 设置用户代理
$response = Curl::get('https://api.example.com/users', userAgent: 'MyApp/1.0');
echo $response->json();

#### 来源页面设置

// 设置来源页面
$response = Curl::get('https://api.example.com/users', referer: 'https://example.com');
echo $response->json();

#### Cookie设置

// 设置Cookie
$response = Curl::get('https://api.example.com/users', cookie: 'session=abc123');
echo $response->json();

#### SSL验证设置

// 禁用SSL验证（仅测试环境）
$response = Curl::get('https://api.example.com/users', verifySsl: false);
echo $response->json();

#### 代理设置

// 设置代理
$response = Curl::get('https://api.example.com/users', proxy: 'http://proxy.example.com:8080');
echo $response->json();

// 设置代理认证
$response = Curl::get('https://api.example.com/users', proxyUserPwd: 'user:password');
echo $response->json();
```

### 响应处理

#### body() - 获取原始响应体

```php
use Kode\Curl\Curl;

$response = Curl::get('https://api.example.com/users');

// 获取原始响应体
echo $response->body();

#### json() - 获取JSON解析结果

// 获取JSON解析结果
$data = $response->json();
echo $data['name'];

#### jsonArray() - 获取数组格式的JSON

// 获取数组格式的JSON
$array = $response->jsonArray();
echo $array[0]['name'];

#### statusCode() - 获取响应状态码

// 获取响应状态码
$statusCode = $response->statusCode();
echo $statusCode; // 200

#### header() - 获取响应头

// 获取响应头
$contentType = $response->header('Content-Type');
echo $contentType; // 'application/json'

#### headers() - 获取所有响应头

// 获取所有响应头
$headers = $response->headers();
print_r($headers);

#### time() - 获取响应时间

// 获取响应时间（毫秒）
$time = $response->time();
echo $time; // 125.5

#### responseInfo() - 获取原始响应头信息

// 获取原始响应头信息
$responseInfo = $response->responseInfo();
print_r($responseInfo);

#### isSuccess() - 判断是否成功

// 判断是否成功
if ($response->isSuccess()) {
    echo '请求成功';
}

#### isRedirect() - 判断是否重定向

// 判断是否重定向
if ($response->isRedirect()) {
    echo '请求重定向';
}
```

### 错误处理

#### isClientError() - 判断是否为客户端错误

```php
use Kode\Curl\Curl;
use Kode\Curl\Exception\CurlException;
use Kode\Curl\Exception\ClientException;
use Kode\Curl\Exception\ServerException;

try {
    $response = Curl::get('https://api.example.com/users');
    
    // 4xx客户端错误
    if ($response->isClientError()) {
        throw new ClientException(
            "客户端请求错误: " . $response->statusCode(),
            $response->statusCode(),
            $response
        );
    }
    
#### isServerError() - 判断是否为服务器错误

    // 5xx服务器错误
    if ($response->isServerError()) {
        throw new ServerException(
            "服务器错误: " . $response->statusCode(),
            $response->statusCode(),
            $response
        );
    }
    
    // 正常响应处理
    $data = $response->json();
    print_r($data);
    
} catch (ClientException $e) {
    // 处理4xx错误
    echo "客户端错误: " . $e->getMessage();
    echo "状态码: " . $e->getCode();
    
} catch (ServerException $e) {
    // 处理5xx错误
    echo "服务器错误: " . $e->getMessage();
    echo "状态码: " . $e->getCode();
    
} catch (CurlException $e) {
    // 处理cURL错误（网络问题等）
    echo "cURL错误: " . $e->getMessage();
    echo "错误码: " . $e->getCode();
    
} catch (\Exception $e) {
    // 处理其他异常
    echo "系统错误: " . $e->getMessage();
}
```

### 重试机制

#### retry() - 自动重试

```php
use Kode\Curl\Curl;

// 自动重试3次，间隔2秒
$response = Curl::get('https://api.example.com/users')
                ->retry(3, 2.0);

echo $response->body();

// POST请求带重试
$response = Curl::post('https://api.example.com/orders', [
    'product_id' => 123,
    'quantity' => 1
])->retry(3, 1.0);

echo $response->json();
```

### 并发请求

#### multi() - 并发执行多个请求

```php
use Kode\Curl\Curl;

// 并发执行多个请求
$responses = Curl::multi()
    ->add('https://api.example.com/users', 'get')
    ->add('https://api.example.com/posts', 'get')
    ->add('https://api.example.com/comments', 'get')
    ->execute();

// 处理响应
$users = $responses['https://api.example.com/users']->json();
$posts = $responses['https://api.example.com/posts']->json();
$comments = $responses['https://api.example.com/comments']->json();

print_r($users);
print_r($posts);
print_r($comments);

#### add() - 添加并发请求

// 带配置的并发请求
$responses = Curl::multi()
    ->add('https://api.example.com/users', 'get', timeout: 10)
    ->add('https://api.example.com/posts', 'get', timeout: 10)
    ->add('https://api.example.com/comments', 'get', timeout: 10)
    ->execute();

// 带请求体的并发请求
$responses = Curl::multi()
    ->add('https://api.example.com/users', 'post', [
        'name' => '张三'
    ])
    ->add('https://api.example.com/posts', 'post', [
        'title' => '文章标题'
    ])
    ->execute();
```

### PHP 8.5+持久化句柄支持

#### sharePersistent() - 共享持久化句柄

```php
use Kode\Curl\Curl;

// PHP 8.5+ 自动使用持久化句柄
// 持久化句柄会在请求结束时保留，避免重复初始化开销
$response1 = Curl::get('https://api.example.com/users');
$response2 = Curl::get('https://api.example.com/posts');
$response3 = Curl::get('https://api.example.com/comments');

// 多个请求复用同一个持久化句柄，提升性能

// 手动设置共享句柄选项
$response = Curl::get('https://api.example.com/users')
                ->sharePersistent()
                ->execute();

echo $response->json();
```

### 高级配置示例

#### 上传文件

```php
use Kode\Curl\Curl;

// 上传文件
$response = Curl::post('https://api.example.com/upload', [
    'file' => new \CURLFile('/path/to/image.jpg', 'image/jpeg', 'image.jpg'),
    'title' => '示例图片'
]);

echo $response->json();

#### 下载文件

// 下载文件
$response = Curl::get('https://api.example.com/file.zip', saveTo: '/path/to/file.zip');

if ($response->isSuccess()) {
    echo '文件下载成功';
}

#### 发送JSON数据

// 发送JSON数据
$response = Curl::post('https://api.example.com/api', [
    'key' => 'value'
], 'json');

echo $response->json();

#### 自定义请求

// 自定义请求
$response = Curl::request('DELETE', 'https://api.example.com/users/1', [
    'id' => 1
]);

echo $response->json();
```

## 二维码生成模块使用示例

二维码生成模块提供了完整的二维码生成功能，支持多种样式定制、输出格式和数据类型。

### 基础使用示例

#### create() - 创建二维码

```php
use Kode\Qrcode\Qr;

// 基础文本二维码
$qr = Qr::create('https://example.com');
$qr->save('/path/to/qrcode.png');

#### toString() - 输出图片数据

// 直接输出图片数据
$imageData = $qr->toString();
header('Content-Type: image/png');
echo $imageData;

#### toDataUri() - 输出Base64编码

// 直接输出Base64编码
$base64 = $qr->toDataUri();
echo "<img src='{$base64}' />";

#### build() - 获取原始对象

// 获取二维码原始对象（用于进一步自定义）
$qrCode = Qr::create('https://example.com');
$builder = $qrCode->build();
```

### 样式定制

#### size() - 设置二维码大小

```php
use Kode\Qrcode\Qr;

// 设置二维码大小
$qr = Qr::create('https://example.com')
        ->size(500)
        ->save('/path/to/qrcode.png');

#### margin() - 设置边距

// 设置边距
$qr = Qr::create('https://example.com')
        ->size(300)
        ->margin(20)
        ->save('/path/to/qrcode.png');

#### foregroundColor() - 设置前景色

// 设置前景色（RGB）
$qr = Qr::create('https://example.com')
        ->foregroundColor(255, 0, 0) // 红色
        ->save('/path/to/qrcode.png');

#### backgroundColor() - 设置背景色

// 设置背景色（RGB）
$qr = Qr::create('https://example.com')
        ->backgroundColor(255, 255, 0) // 黄色背景
        ->save('/path/to/qrcode.png');

// 同时设置前景和背景色
$qr = Qr::create('https://example.com')
        ->foregroundColor(0, 0, 255) // 蓝色
        ->backgroundColor(255, 255, 255) // 白色背景
        ->save('/path/to/qrcode.png');

#### errorCorrectionLevel() - 设置错误纠正级别

// 设置错误纠正级别（1-5，对应L/M/Q/H级别）
$qr = Qr::create('https://example.com')
        ->errorCorrectionLevel(5) // 最高级别H，可修复30%错误
        ->save('/path/to/qrcode.png');

#### roundDots() - 圆角点样式

// 圆角点样式
$qr = Qr::create('https://example.com')
        ->roundDots(true) // 启用圆角点
        ->save('/path/to/qrcode.png');

#### circularDots() - 圆形点样式

// 圆形点样式（带大小控制）
$qr = Qr::create('https://example.com')
        ->circularDots(true, 12) // 启用圆形点，尺寸比例12
        ->save('/path/to/qrcode.png');

#### gradient() - 渐变颜色

// 渐变颜色
$qr = Qr::create('https://example.com')
        ->gradient(
            new \Endroid\QrCode\Color\Color(255, 0, 0),     // 起始颜色（红色）
            new \Endroid\QrCode\Color\Color(0, 0, 255),     // 结束颜色（蓝色）
            'vertical' // 渐变方向：horizontal/vertical/diagonal/diagonal_inverse
        )
        ->save('/path/to/qrcode.png');

// 组合样式
$qr = Qr::create('https://example.com')
        ->size(400)
        ->margin(15)
        ->foregroundColor(0, 128, 0) // 绿色
        ->backgroundColor(240, 240, 240) // 浅灰背景
        ->errorCorrectionLevel(4) // Q级别
        ->roundDots(true)
        ->save('/path/to/qrcode.png');
```

### Logo嵌入

#### logo() - 添加Logo

```php
use Kode\Qrcode\Qr;

// 添加Logo
$qr = Qr::create('https://example.com')
        ->size(400)
        ->logo('/path/to/logo.png') // Logo图片路径
        ->save('/path/to/qrcode_with_logo.png');

// 设置Logo大小比例
$qr = Qr::create('https://example.com')
        ->size(400)
        ->logo('/path/to/logo.png', 0.3) // Logo占二维码的30%
        ->save('/path/to/qrcode_with_logo.png');

// 设置Logo大小比例和边距
$qr = Qr::create('https://example.com')
        ->size(400)
        ->logo('/path/to/logo.png', 0.25) // Logo占25%
        ->save('/path/to/qrcode_with_logo.png');

// 组合Logo和其他样式
$qr = Qr::create('https://example.com')
        ->size(500)
        ->foregroundColor(0, 0, 0)
        ->backgroundColor(255, 255, 255)
        ->errorCorrectionLevel(5) // H级别
        ->logo('/path/to/logo.png', 0.3)
        ->save('/path/to/qrcode_with_logo.png');
```

### 标签文字

#### label() - 添加标签

```php
use Kode\Qrcode\Qr;

// 添加标签
$qr = Qr::create('https://example.com')
        ->size(400)
        ->label('官方网站') // 标签文字
        ->save('/path/to/qrcode_with_label.png');

// 设置标签字体大小
$qr = Qr::create('https://example.com')
        ->size(400)
        ->label('官方网站', 24) // 字体大小24px
        ->save('/path/to/qrcode_with_label.png');

// 标签文字默认显示在二维码下方，无需额外设置位置

// 组合标签和Logo
$qr = Qr::create('https://example.com')
        ->size(500)
        ->logo('/path/to/logo.png', 0.25)
        ->label('扫描访问', 20)
        ->save('/path/to/qrcode_complete.png');
```

### 多种输出格式

#### save() - 保存为PNG格式

```php
use Kode\Qrcode\Qr;

// PNG格式（默认）
$qr = Qr::create('https://example.com');
$qr->save('/path/to/qrcode.png');
$pngData = $qr->toString(); // PNG二进制数据

#### asSvg() - 输出SVG格式

// SVG格式（矢量图，无限放大不失真）
$qr = Qr::create('https://example.com')
        ->asSvg() // 设置为SVG格式
        ->save('/path/to/qrcode.svg');
$svgData = $qr->toString(); // SVG字符串数据

#### asWebP() - 输出WebP格式

// WebP格式（现代图片格式，更小体积）
$qr = Qr::create('https://example.com')
        ->asWebP()
        ->save('/path/to/qrcode.webp');
$webpData = $qr->toString(); // WebP二进制数据

#### asEps() - 输出EPS格式

// EPS格式（印刷级矢量格式）
$qr = Qr::create('https://example.com')
        ->asEps()
        ->save('/path/to/qrcode.eps');
$epsData = $qr->toString(); // EPS字符串数据

#### toDataUri() - 输出Data URI

// 输出Data URI
$qr = Qr::create('https://example.com');
$dataUri = $qr->toDataUri(); // data:image/png;base64,...
echo "<img src='{$dataUri}' />";

// 根据用途选择格式
// PNG - 通用
// SVG - 打印、放大
// WebP - 网页加载优化
// EPS - 专业印刷
```

### 多种数据类型支持

#### url() - URL二维码

```php
use Kode\Qrcode\Qr;

// URL二维码
$qr = Qr::url('https://example.com')
        ->size(300)
        ->save('/path/to/url_qr.png');

#### wifi() - WiFi二维码

// WiFi二维码
$qr = Qr::wifi('MyWiFi', 'password123', 'wpa') // WiFi名、密码、加密类型
        ->size(300)
        ->save('/path/to/wifi_qr.png');

// 隐藏WiFi
$qr = Qr::wifi('MyWiFi', 'password123', 'wpa', true) // true表示隐藏网络
        ->size(300)
        ->save('/path/to/wifi_hidden_qr.png');

#### email() - 邮件二维码

// 邮件二维码
$qr = Qr::email('user@example.com', '主题', '内容')
        ->size(300)
        ->save('/path_to/email_qr.png');

#### phone() - 电话二维码

// 电话二维码
$qr = Qr::phone('13800138000')
        ->size(300)
        ->save('/path/to/phone_qr.png');

#### sms() - 短信二维码

// 短信二维码
$qr = Qr::sms('13800138000', '您好，这是测试短信')
        ->size(300)
        ->save('/path/to/sms_qr.png');

#### geo() - 位置二维码

// 位置二维码
$qr = Qr::geo(39.9042, 116.4074) // 经度、纬度
        ->size(300)
        ->save('/path/to/geo_qr.png');

#### bitcoin() - 比特币二维码

// 比特币二维码
$qr = Qr::bitcoin('1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa', 0.5) // 地址、金额（BTC）
        ->size(300)
        ->save('/path/to/bitcoin_qr.png');

#### event() - 日历事件二维码

// 日历事件二维码
$qr = Qr::event(
        '圣诞节聚会',           // 事件标题
        '2024-12-25 10:00:00', // 开始时间
        '2024-12-25 12:00:00', // 结束时间
        '北京市朝阳区某某路'    // 地点
    )
    ->size(300)
    ->save('/path/to/calendar_qr.png');

// vCard名片二维码
$contactData = [
    'email' => 'zhangsan@example.com',
    'phone' => '13800138000',
    'org' => '示例公司',           // organization
    'title' => '工程师',
    'address' => '某某路123号'
];
$qr = Qr::vcard($contactData, '三', '张') // contact数组、姓、名
        ->size(300)
        ->save('/path/to/vcard_qr.png');
```

### 编码模式设置

```php
use Kode\Qrcode\Qr;

// 自动编码（默认）
$qr = Qr::create('https://example.com');
$qr->save('/path/to/qrcode.png');

// 数字编码（纯数字）
$qr = Qr::create('1234567890')
        ->encodingMode('numeric')
        ->save('/path/to/numeric_qr.png');

// 字母数字编码
$qr = Qr::create('HELLO123')
        ->encodingMode('alphanumeric')
        ->save('/path/to/alpha_qr.png');

// 二进制编码（默认）
$qr = Qr::create('Hello World')
        ->encodingMode('byte')
        ->save('/path/to/byte_qr.png');

// 日文编码
$qr = Qr::create('こんにちは')
        ->encodingMode('kanji')
        ->save('/path/to/kanji_qr.png');
```

### 圆角块大小模式

```php
use Kode\Qrcode\Qr;

// 最小圆角块（minus）
$qr = Qr::create('https://example.com')
        ->roundStyle('minus')
        ->save('/path/to/qrcode.png');

// 标称圆角块（nominal，默认）
$qr = Qr::create('https://example.com')
        ->roundStyle('nominal')
        ->save('/path/to/qrcode.png');

// 最大圆角块（plus）
$qr = Qr::create('https://example.com')
        ->roundStyle('plus')
        ->save('/path/to/qrcode.png');

// 极高圆角块（murky）
$qr = Qr::create('https://example.com')
        ->roundStyle('murky')
        ->save('/path/to/qrcode.png');
```

### 完整自定义示例

```php
use Kode\Qrcode\Qr;

// 完整自定义的二维码
$qr = Qr::create('https://example.com')
    // 基本设置
    ->size(500)
    ->margin(15)
    ->errorCorrectionLevel(5) // H级别
    
    // 颜色设置
    ->foregroundColor(0, 102, 204) // 蓝色前景
    ->backgroundColor(255, 255, 255) // 白色背景
    
    // 样式设置
    ->roundDots(true)
    
    // Logo设置
    ->logo('/path/to/logo.png', 0.25)
    
    // 标签设置
    ->label('官方网站', 20) // 标签显示在二维码下方
    
    // 保存文件
    ->save('/path/to/custom_qrcode.png');

// 生成Data URI用于网页显示
$base64 = $qr->toDataUri();
echo "<img src='{$base64}' alt='二维码' />";

// 带渐变效果的二维码
$qrGradient = Qr::create('https://example.com')
    ->size(400)
    ->margin(10)
    ->errorCorrectionLevel(4) // Q级别
    ->roundDots(true)
    ->gradient(
        new \Endroid\QrCode\Color\Color(255, 87, 34),  // 橙红色
        new \Endroid\QrCode\Color\Color(33, 150, 243), // 蓝色
        'diagonal' // 对角线渐变
    )
    ->save('/path/to/gradient_qrcode.png');

// 带Logo和渐变的组合
$qrCombo = Qr::create('https://example.com')
    ->size(600)
    ->margin(20)
    ->foregroundColor(76, 175, 80) // 绿色
    ->backgroundColor(245, 245, 245) // 浅灰背景
    ->errorCorrectionLevel(5) // H级别
    ->circularDots(true, 12)
    ->logo('/path/to/logo.png', 0.3)
    ->label('扫码访问', 22)
    ->save('/path/to/combo_qrcode.png');
```

## 全局助手函数使用示例

全局助手函数提供了简化的调用方式，无需手动引入类文件即可使用。所有助手函数都遵循`模块_方法`的命名规范。

### 数组助手函数

```php
// 获取数组第一个元素
$first = arr_first([1, 2, 3]); // 1
$first = arr_first(['a' => 1, 'b' => 2]); // 1

// 获取数组最后一个元素
$last = arr_last([1, 2, 3]); // 3
$last = arr_last(['a' => 1, 'b' => 2]); // 2

// 查找满足条件的元素
$found = arr_find([1, 2, 3, 4, 5], function($n) {
    return $n > 3;
}); // 4

// 查找满足条件的元素的键
$key = arr_find_key(['a' => 1, 'b' => 2, 'c' => 3], function($n) {
    return $n > 2;
}); // 'c'

// 数组是否存在满足条件的元素
$any = arr_any([1, 2, 3, 4, 5], function($n) {
    return $n > 3;
}); // true

// 数组是否所有元素都满足条件
$all = arr_all([1, 2, 3, 4, 5], function($n) {
    return $n > 0;
}); // true

// 数组深度合并
$array1 = ['user' => ['name' => '张三', 'age' => 25]];
$array2 = ['user' => ['age' => 26, 'email' => 'user@example.com']];
$merged = arr_deep_merge($array1, $array2);
// ['user' => ['name' => '张三', 'age' => 26, 'email' => 'user@example.com']]

// 获取数组值
$array = ['user' => ['name' => '张三', 'age' => 25]];
$value = arr_get($array, 'user.name'); // '张三'
$value = arr_get($array, 'user.email', 'default'); // 'default'

// 设置数组值
$array = arr_set($array, 'user.email', 'user@example.com');
// ['user' => ['name' => '张三', 'age' => 25, 'email' => 'user@example.com']]

// 检查数组键是否存在
$exists = arr_has($array, 'user.name'); // true
$exists = arr_has($array, 'user.phone'); // false

// 多维数组排序
$data = [
    ['name' => '张三', 'age' => 25, 'score' => 90],
    ['name' => '李四', 'age' => 22, 'score' => 85],
    ['name' => '王五', 'age' => 28, 'score' => 95]
];
$sorted = arr_multi_sort($data, ['age', 'score'], ['asc', 'desc']);
// 按年龄升序、分数降序排序

// 数组转树形结构
$list = [
    ['id' => 1, 'parent_id' => 0, 'name' => '根节点'],
    ['id' => 2, 'parent_id' => 1, 'name' => '子节点1'],
    ['id' => 3, 'parent_id' => 1, 'name' => '子节点2']
];
$tree = arr_tree($list, 'id', 'parent_id');

// 树形结构转数组
$array = arr_list($tree);
```

### 字符串助手函数

```php
// 字符串脱敏
$masked = str_mask('13812345678', 3, 4); // '138****5678'
$masked = str_mask('user@example.com', 1, -1); // 'u***@example.com'

// 手机号脱敏
$phoneMasked = str_mask_phone('13812345678'); // '138****5678'

// 邮箱脱敏
$emailMasked = str_mask_email('user@example.com'); // 'u***@example.com'

// 身份证号脱敏
$idCardMasked = str_mask_id_card('110101199001011234'); // '110101********1234'

// 字符串截断
$truncated = str_truncate('这是一段很长的文本内容', 10); // '这是一段很长的文...'
$truncated = str_truncate('Hello World', 5, '...'); // 'Hello...'

// 字符串长度限制
$limited = str_limit('这是一段很长的文本内容', 10); // '这是一段很长的文...'

// 驼峰转下划线
$snake = str_snake('helloWorld'); // 'hello_world'
$snake = str_snake('HelloWorld'); // 'hello_world'

// 下划线转驼峰
$camel = str_camel('hello_world'); // 'helloWorld'
$camel = str_camel('hello-world', '-'); // 'helloWorld'

// 首字母大写
$studly = str_studly('hello_world'); // 'HelloWorld'

// 字符串包含判断
$contains = str_contains('hello world', 'world'); // true
$contains = str_contains('hello world', 'php'); // false

// 字符串开头判断
$startsWith = str_starts_with('hello world', 'hello'); // true
$startsWith = str_starts_with('hello world', 'world'); // false

// 字符串结尾判断
$endsWith = str_ends_with('hello world', 'world'); // true
$endsWith = str_ends_with('hello world', 'hello'); // false

// 字符串转Base64
$base64 = str_to_base64('hello'); // 'aGVsbG8='

// Base64转字符串
$decoded = str_from_base64('aGVsbG8='); // 'hello'

// 字符串转JSON
$json = str_to_json(['name' => '张三', 'age' => 25]); // '{"name":"张三","age":25}'

// JSON转字符串
$data = str_from_json('{"name":"张三","age":25}'); // ['name' => '张三', 'age' => 25]

// 字符串转数组
$array = str_to_array('a,b,c', ','); // ['a', 'b', 'c']
$array = str_to_array('a|b|c', '|'); // ['a', 'b', 'c']

// 数组转字符串
$string = str_from_array(['a', 'b', 'c'], ','); // 'a,b,c'
$string = str_from_array(['a', 'b', 'c'], '|'); // 'a|b|c'

// 生成随机字符串
$random = str_random(16); // 生成16位随机字符串

// 生成UUID
$uuid = str_uuid(); // 生成UUID v4

// 字符串转小写
$lower = str_lower('Hello World'); // 'hello world'

// 字符串转大写
$upper = str_upper('Hello World'); // 'HELLO WORLD'

// 字符串首字母大写
$ucfirst = str_ucfirst('hello world'); // 'Hello world'

// 字符串每个单词首字母大写
$ucwords = str_ucwords('hello world'); // 'Hello World'

// 字符串反转
$reversed = str_reverse('hello'); // 'olleh'

// 字符串重复
$repeated = str_repeat('hello', 3); // 'hellohellohello'

// 字符串替换
$replaced = str_replace('hello world', 'world', 'php'); // 'hello php'

// 字符串去除空格
$trimmed = str_trim('  hello world  '); // 'hello world'

// 字符串去除左侧空格
$ltrimmed = str_ltrim('  hello world  '); // 'hello world  '

// 字符串去除右侧空格
$rtrimmed = str_rtrim('  hello world  '); // '  hello world'

// 字符串长度
$length = str_length('hello'); // 5
$length = str_length('你好'); // 2（中文字符）

// 字符串转二进制
$binary = str_to_binary('hello'); // '01101000 01100101 01101100 01101100 01101111'

// 二进制转字符串
$string = str_from_binary('01101000 01100101 01101100 01101100 01101111'); // 'hello'

// 字符串转十六进制
$hex = str_to_hex('hello'); // '68656c6c6f'

// 十六进制转字符串
$string = str_from_hex('68656c6c6f'); // 'hello'

// 字符串MD5哈希
$md5 = str_md5('hello'); // '5d41402abc4b2a76b9719d911017c592'

// 字符串SHA1哈希
$sha1 = str_sha1('hello'); // 'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d'

// 字符串SHA256哈希
$sha256 = str_sha256('hello'); // '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824'

// 字符串验证
$isEmail = str_is_email('user@example.com'); // true
$isPhone = str_is_phone('13812345678'); // true
$isIdCard = str_is_id_card('110101199001011234'); // true
$isUrl = str_is_url('https://example.com'); // true
$isIp = str_is_ip('192.168.1.1'); // true

// 字符串相似度
$similarity = str_similarity('hello', 'hello'); // 1.0
$similarity = str_similarity('hello', 'world'); // 0.2

// 字符串编辑距离
$distance = str_distance('hello', 'hello'); // 0
$distance = str_distance('hello', 'world'); // 4
```

### 时间助手函数

```php
// 获取当前时间
$now = time_now(); // '2025-12-26 12:00:00'
$now = time_now('Y-m-d'); // '2025-12-26'

// 格式化时间
$formatted = time_format(time(), 'Y-m-d H:i:s'); // '2025-12-26 12:00:00'

// 人性化时间差
$human = time_human(time() - 60); // '1分钟前'
$human = time_human(time() - 3600); // '1小时前'
$human = time_human(time() - 86400); // '1天前'

// 获取今天日期
$today = time_today(); // '2025-12-26'
$today = time_today('Y-m-d H:i:s'); // '2025-12-26 00:00:00'

// 获取昨天日期
$yesterday = time_yesterday(); // '2025-12-25'

// 获取明天日期
$tomorrow = time_tomorrow(); // '2025-12-27'

// 获取本周开始时间
$weekStart = time_week_start(); // 本周一00:00:00的时间戳
$weekStartFormatted = time_format(time_week_start(), 'Y-m-d H:i:s'); // '2025-12-22 00:00:00'

// 获取本周结束时间
$weekEnd = time_week_end(); // 本周日23:59:59的时间戳
$weekEndFormatted = time_format(time_week_end(), 'Y-m-d H:i:s'); // '2025-12-28 23:59:59'

// 获取上周开始时间
$lastWeekStart = time_last_week_start(); // 上周一00:00:00的时间戳

// 获取上周结束时间
$lastWeekEnd = time_last_week_end(); // 上周日23:59:59的时间戳

// 获取本月开始时间
$monthStart = time_month_start(); // 本月1日00:00:00的时间戳
$monthStartFormatted = time_format(time_month_start(), 'Y-m-d H:i:s'); // '2025-12-01 00:00:00'

// 获取本月结束时间
$monthEnd = time_month_end(); // 本月最后一天23:59:59的时间戳
$monthEndFormatted = time_format(time_month_end(), 'Y-m-d H:i:s'); // '2025-12-31 23:59:59'

// 获取上月开始时间
$lastMonthStart = time_last_month_start(); // 上月1日00:00:00的时间戳

// 获取上月结束时间
$lastMonthEnd = time_last_month_end(); // 上月最后一天23:59:59的时间戳

// 获取本年开始时间
$yearStart = time_year_start(); // 本年1月1日00:00:00的时间戳
$yearStartFormatted = time_format(time_year_start(), 'Y-m-d H:i:s'); // '2025-01-01 00:00:00'

// 获取本年结束时间
$yearEnd = time_year_end(); // 本年12月31日23:59:59的时间戳
$yearEndFormatted = time_format(time_year_end(), 'Y-m-d H:i:s'); // '2025-12-31 23:59:59'

// 获取上年开始时间
$lastYearStart = time_last_year_start(); // 上年1月1日00:00:00的时间戳

// 获取上年结束时间
$lastYearEnd = time_last_year_end(); // 上年12月31日23:59:59的时间戳

// 计算年龄
$age = time_age('1990-01-01'); // 35（假设当前是2025年）

// 判断是否是今天
$isToday = time_is_today(time()); // true

// 判断是否是昨天
$isYesterday = time_is_yesterday(time() - 86400); // true

// 判断是否是明天
$isTomorrow = time_is_tomorrow(time() + 86400); // true

// 判断是否是本周
$isThisWeek = time_is_this_week(time()); // true

// 判断是否是本月
$isThisMonth = time_is_this_month(time()); // true

// 判断是否是本年
$isThisYear = time_is_this_year(time()); // true

// 获取某个月的天数
$days = time_days_in_month(12); // 31
$days = time_days_in_month(2, 2024); // 29（闰年）

// 获取某天是周几
$weekday = time_day_of_week(time()); // 0-6（0表示周日）
$weekdayName = time_day_of_week_name(time()); // '周五'

// 获取某天是本年第几天
$dayOfYear = time_day_of_year(time()); // 360

// 获取某天是本年第几周
$weekOfYear = time_week_of_year(time()); // 52

// 时间字符串转时间戳
$timestamp = time_to_timestamp('2025-12-26 12:00:00'); // 1735200000

// 时间戳转毫秒
$millisecond = time_to_millisecond(time()); // 1735200000000

// 毫秒转时间戳
$timestamp = time_from_millisecond(1735200000000); // 1735200000

// 获取当前毫秒时间戳
$millisecond = time_millisecond(); // 当前时间的毫秒时间戳

// 获取当前微秒时间戳
$microsecond = time_microsecond(); // 当前时间的微秒时间戳

// 获取当前时间戳（带微秒）
$microtime = time_microtime(); // 1735200000.123456

// 获取当前时区
$timezone = time_timezone(); // 'Asia/Shanghai'

// 设置时区
time_set_timezone('UTC'); // 设置时区为UTC
```

### 数学助手函数

```php
// 高精度加法
$result = math_add('1.1', '2.2'); // '3.3'
$result = math_add('1.1', '2.2', 2); // '3.30'

// 高精度减法
$result = math_sub('3.3', '1.1'); // '2.2'
$result = math_sub('3.3', '1.1', 2); // '2.20'

// 高精度乘法
$result = math_mul('1.5', '2'); // '3.0'
$result = math_mul('1.5', '2', 2); // '3.00'

// 高精度除法
$result = math_div('6', '2'); // '3'
$result = math_div('10', '3', 2); // '3.33'

// 取模运算
$result = math_mod(10, 3); // 1

// 幂运算
$result = math_pow(2, 10); // 1024

// 平方根运算
$result = math_sqrt(16); // 4

// 四舍五入
$result = math_round(3.14159, 2); // 3.14

// 向上取整
$result = math_ceil(3.2); // 4

// 向下取整
$result = math_floor(3.8); // 3

// 绝对值
$result = math_abs(-10); // 10

// 最大值
$result = math_max(1, 3, 2); // 3

// 最小值
$result = math_min(1, 3, 2); // 1

// 求和
$result = math_sum([1, 2, 3, 4, 5]); // 15

// 平均值
$result = math_avg([1, 2, 3, 4, 5]); // 3

// 中位数
$result = math_median([1, 2, 3, 4, 5]); // 3

// 标准差
$result = math_std_dev([1, 2, 3, 4, 5]); // 1.414

// 计算折扣价
$result = math_discount(100, 0.2); // 80（打8折）

// 计算税额
$result = math_tax(100, 0.13); // 13（13%税）

// 计算利润率
$result = math_profit_rate(100, 80); // 0.25

// 计算增长率
$result = math_growth_rate(100, 120); // 0.2

// 计算复利
$result = math_compound_interest(1000, 0.05, 12); // 1795.86

// 计算简单利息
$result = math_simple_interest(1000, 0.05, 12); // 600

// 计算分期付款
$result = math_installment(10000, 0.005, 12); // 861.85

// 计算现值
$result = math_present_value(10000, 0.005, 12); // 9419.05

// 计算未来值
$result = math_future_value(1000, 0.005, 12); // 1061.68

// 判断是否为素数
$result = math_is_prime(7); // true

// 计算阶乘
$result = math_factorial(5); // 120

// 计算最大公约数
$result = math_gcd(12, 18); // 6

// 计算最小公倍数
$result = math_lcm(4, 6); // 12

// 格式化数字
$result = math_format(1234567.89, 2, true); // 1,234,567.89
```

### 加密助手函数

```php
// MD5加密（支持加盐）
$md5 = crypto_md5('123456', 'salt123'); // 加盐MD5

// 密码哈希
$hash = crypto_password_hash('123456'); // 密码哈希

// 密码验证
$verify = crypto_password_verify('123456', $hash); // true

// SSL加密
$encrypted = crypto_ssl_encrypt('敏感数据', '1234567890abcdef'); // 加密

// SSL解密
$decrypted = crypto_ssl_decrypt($encrypted, '1234567890abcdef'); // 解密

// HMAC签名
$hmac = crypto_hmac('数据', 'key', 'sha256'); // HMAC签名
```

### 地理位置助手函数

```php
// 计算两点距离（米）
$distance = geo_distance(39.9042, 116.4074, 31.2304, 121.4737); // 米

// 计算两点距离（公里）
$distanceKm = geo_distance_km(39.9042, 116.4074, 31.2304, 121.4737); // 公里

// 验证纬度
$validLat = geo_validate_lat(39.9042); // true

// 验证经度
$validLon = geo_validate_lon(116.4074); // true

// 验证坐标
$validCoord = geo_validate_coord(39.9042, 116.4074); // true
```

### IP地址助手函数

```php
// 获取客户端IP
$ip = ip_get(); // 客户端IP

// 验证IP地址
$valid = ip_validate('192.168.1.1'); // true

// 是否为私有IP
$private = ip_is_private('192.168.1.1'); // true

// 是否为公网IP
$public = ip_is_public('8.8.8.8'); // true

// IP地址转长整型
$long = ip_to_long('192.168.1.1'); // 3232235777

// 长整型转IP地址
$ipStr = ip_to_string(3232235777); // '192.168.1.1'
```

### 实际应用场景

```php
// 订单时间显示
$createdAt = time() - 3600;
$displayTime = time_human($createdAt); // '1小时前'

// 数据统计
$weekStart = time_week_start();
$weekEnd = time_week_end();
// 查询本周数据：WHERE created_at >= $weekStart AND created_at <= $weekEnd

// 用户信息脱敏
$phoneMasked = str_mask_phone('13812345678'); // '138****5678'
$emailMasked = str_mask_email('user@example.com'); // 'u***@example.com'

// 金额计算
$subtotal = math_add('99.99', '49.99'); // 149.98
$discount = math_discount($subtotal, '0.9'); // 134.98
$tax = math_tax($discount, '0.13'); // 17.55
$total = math_add($discount, $tax, 2); // 152.53

// 距离计算
$distance = geo_distance_km(39.9042, 116.4074, 31.2304, 121.4737); // 约1067公里

// IP地址处理
$ip = ip_get();
if (ip_is_private($ip)) {
    // 内网IP
} else {
    // 公网IP
}

// 数组操作
$data = ['user' => ['name' => '张三', 'age' => 25]];
$name = arr_get($data, 'user.name'); // '张三'
$data = arr_set($data, 'user.email', 'user@example.com');

// 字符串处理
$random = str_random(16); // 生成随机字符串
$uuid = str_uuid(); // 生成UUID
$base64 = str_to_base64('hello'); // 'aGVsbG8='
```
```

## 基本使用

### 数组处理使用示例

```php
use Kode\Array\Arr;

// 数组转树形结构
$list = [
    ['id' => 1, 'parent_id' => 0, 'name' => '根节点'],
    ['id' => 2, 'parent_id' => 1, 'name' => '子节点1'],
    ['id' => 3, 'parent_id' => 1, 'name' => '子节点2'],
    ['id' => 4, 'parent_id' => 2, 'name' => '孙节点1']
];

$tree = Arr::tree($list);
// 输出:
// [
//     'id' => 1,
//     'parent_id' => 0,
//     'name' => '根节点',
//     'children' => [
//         ['id' => 2, 'parent_id' => 1, 'name' => '子节点1', 'children' => [['id' => 4, 'parent_id' => 2, 'name' => '孙节点1', 'children' => []]]],
//         ['id' => 3, 'parent_id' => 1, 'name' => '子节点2', 'children' => []]
//     ]
// ]

// 树形结构转数组
$array = Arr::list($tree);

// 数组转层级结构
$levelList = Arr::level($list);

// 数组转路径结构
$pathList = Arr::path($list);

// 数组操作
$value = Arr::get($array, 'key', 'default');
$array = Arr::set($array, 'key', 'value');
$exists = Arr::has($array, 'key');
$only = Arr::only($array, ['key1', 'key2']);
$except = Arr::except($array, ['key1', 'key2']);

// 数组深度合并
$array1 = [
    'user' => [
        'name' => '张三',
        'age' => 25
    ],
    'settings' => [
        'theme' => 'dark'
    ]
];

$array2 = [
    'user' => [
        'age' => 26,
        'email' => 'user@example.com'
    ],
    'settings' => [
        'language' => 'zh-CN'
    ]
];

$merged = Arr::deepMerge($array1, $array2);
// 输出:
// [
//     'user' => [
//         'name' => '张三',
//         'age' => 26,
//         'email' => 'user@example.com'
//     ],
//     'settings' => [
//         'theme' => 'dark',
//         'language' => 'zh-CN'
//     ]
// ]

// 多维数组排序
$data = [
    ['name' => '张三', 'age' => 25, 'score' => 90],
    ['name' => '李四', 'age' => 22, 'score' => 85],
    ['name' => '王五', 'age' => 28, 'score' => 95]
];

// 按年龄升序，分数降序排序
$sorted = Arr::multiSort($data, ['age', 'score'], ['asc', 'desc']);
// 输出:
// [
//     ['name' => '李四', 'age' => 22, 'score' => 85],
//     ['name' => '张三', 'age' => 25, 'score' => 90],
//     ['name' => '王五', 'age' => 28, 'score' => 95]
// ]

// 多维数组去重
$data = [
    ['id' => 1, 'name' => '张三'],
    ['id' => 2, 'name' => '李四'],
    ['id' => 1, 'name' => '张三']
];

$unique = Arr::multiUnique($data, 'id');
// 输出:
// [
//     ['id' => 1, 'name' => '张三'],
//     ['id' => 2, 'name' => '李四']
// ]

// 数组首元素（PHP 8.4+使用原生array_first）
$first = Arr::first([1, 2, 3, 4, 5]); // 1

// 数组尾元素（PHP 8.4+使用原生array_last）
$last = Arr::last([1, 2, 3, 4, 5]); // 5

// 查找满足条件的元素
$found = Arr::find([1, 2, 3, 4, 5], fn($n) => $n > 2); // 3

// 查找满足条件的元素键名
$foundKey = Arr::findKey(['a' => 1, 'b' => 2, 'c' => 3], fn($n) => $n > 1); // 'b'

// 检查是否存在满足条件的元素（PHP 8.4+使用原生array_any）
$hasAny = Arr::any([1, 2, 3, 4, 5], fn($n) => $n > 3); // true

// 检查是否所有元素都满足条件（PHP 8.4+使用原生array_all）
$allMatch = Arr::all([1, 2, 3, 4, 5], fn($n) => $n > 0); // true
```

### 字符串处理使用示例

```php
use Kode\String\Str;

// 生成随机字符串
$random = Str::random(16);

// 生成UUID
$uuid = Str::uuid();

// 命名转换
$camel = Str::camel('hello-world'); // helloWorld
$snake = Str::snake('helloWorld'); // hello_world
$studly = Str::studly('hello-world'); // HelloWorld

// 字符串处理
$escaped = Str::escape('Hello "World"'); // Hello \"World\"
$unescaped = Str::unescape('Hello \"World\"'); // Hello "World"
$trimmed = Str::trim('  Hello World  '); // Hello World

// 字符串脱敏（默认参数）
$maskedPhone = Str::maskPhone('13800138000'); // 138****8000
$maskedIdCard = Str::maskIdCard('110101199003074567'); // 110101********4567
$maskedEmail = Str::maskEmail('user@example.com'); // us********@example.com
$maskedBankCard = Str::maskBankCard('6222021234567890'); // 622202********7890
$maskedCarPlate = Str::maskCarPlate('京A12345'); // 京A***45
$maskedName = Str::maskName('张三'); // 张*

// 字符串脱敏（自定义参数）
$maskedPhoneCustom = Str::maskPhone('13800138000', 2, 2); // 13****00
$maskedIdCardCustom = Str::maskIdCard('110101199003074567', 4, 2); // 1101**********67
$maskedEmailCustom = Str::maskEmail('user@example.com', 3); // use*******@example.com
$maskedBankCardCustom = Str::maskBankCard('6222021234567890', 4, 2); // 6222**********90
$maskedCarPlateCustom = Str::maskCarPlate('京A12345', 1, 1); // 京****5
$maskedNameCustom = Str::maskName('张三', 2); // 张三（不脱敏）

// 字符串验证
$isPhone = Str::validatePhone('13800138000'); // true
$isIdCard = Str::validateIdCard('110101199003074567'); // true
$isEmail = Str::validateEmail('user@example.com'); // true
$isCarPlate = Str::validateCarPlate('京A12345'); // true
$isBankCard = Str::validateBankCard('6222021234567890'); // true

// 收货地址解析
$address = "张三 13800138000 北京市朝阳区建国路88号 100020";
$parsed = Str::parseAddress($address);
// 输出:
// [
//     'name' => '张三',
//     'phone' => '13800138000',
//     'province' => '北京市',
//     'city' => '市',
//     'district' => '朝阳区',
//     'detail' => '建国路88号',
//     'zip' => '100020'
// ]

// 字符串截断
$truncated = Str::truncate('这是一段很长的文本内容', 10); // 这是一段很...

// 字符串限制
$limited = Str::limit('这是一段很长的文本内容', 10); // 这是一段很...

// 驼峰转下划线
$snake = Str::snake('helloWorld'); // hello_world

// 下划线转驼峰
$camel = Str::camel('hello_world'); // helloWorld

// 字符串是否包含
$contains = Str::contains('hello world', 'world'); // true

// 字符串是否以开头
$startsWith = Str::startsWith('hello world', 'hello'); // true

// 字符串是否以结尾
$endsWith = Str::endsWith('hello world', 'world'); // true

// 字符串替换多个
$replaced = Str::replaceArray('Hello :name, welcome to :place', [':name' => '张三', ':place' => '北京']); // Hello 张三, welcome to 北京

// 字符串删除
$removed = Str::remove('hello world', 'world'); // hello 

// 字符串删除多个
$removedArray = Str::removeArray('hello world', ['hello', 'world']); //  

// 字符串连接
$joined = Str::join(['a', 'b', 'c'], '-'); // a-b-c

// 字符串去重
$unique = Str::unique('aabbccdd'); // abcd

// 字符串打乱
$shuffled = Str::shuffle('abcde'); // 随机打乱

// 字符串截取
$sub = Str::substr('hello world', 0, 5); // hello

// 字符串截取多字节
$mbSub = Str::mbSubstr('你好世界', 0, 2); // 你好

// 字符串长度
$length = Str::length('hello'); // 5

// 字符串多字节长度
$mbLength = Str::mbLength('你好'); // 2

// 字符串转二进制
$binary = Str::toBinary('hello'); // 0110100001100101011011000110110001101111

// 二进制转字符串
$fromBinary = Str::fromBinary('0110100001100101011011000110110001101111'); // hello

// 字符串转十六进制
$hex = Str::toHex('hello'); // 68656c6c6f

// 十六进制转字符串
$fromHex = Str::fromHex('68656c6c6f'); // hello

// 字符串转Base64
$base64 = Str::toBase64('hello'); // aGVsbG8=

// Base64转字符串
$fromBase64 = Str::fromBase64('aGVsbG8='); // hello

// 字符串转URL编码
$urlEncoded = Str::toUrlEncode('hello world'); // hello%20world

// URL编码转字符串
$urlDecoded = Str::fromUrlDecode('hello%20world'); // hello world

// 字符串压缩
$compressed = Str::compress('这是一段需要压缩的文本内容');

// 字符串解压
$decompressed = Str::decompress($compressed);
```

### 地理位置处理使用示例

```php
use Kode\Geo\Geo;

// 计算两点间距离（单位：千米）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737); // 北京到上海的距离

// 计算两点间距离（单位：米）
$distanceMeters = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'm'); // 北京到上海的距离（米）

// 验证经度
$isValidLng = Geo::isValidLng(116.4074); // true

// 验证纬度
$isValidLat = Geo::isValidLat(39.9042); // true

// 验证坐标
$isValidCoordinate = Geo::isValidCoordinate(39.9042, 116.4074); // true

// 经度转DMS格式
$lngDms = Geo::lngToDms(116.4074); // 116°24'26.64"E

// 纬度转DMS格式
$latDms = Geo::latToDms(39.9042); // 39°54'15.12"N

// DMS格式转经度
$lng = Geo::dmsToLng('116°24\'26.64"E'); // 116.4074

// DMS格式转纬度
$lat = Geo::dmsToLat('39°54\'15.12"N'); // 39.9042

// 计算中点坐标
$midpoint = Geo::midpoint(39.9042, 116.4074, 31.2304, 121.4737);
// 输出: ['lat' => 35.5673, 'lng' => 118.9406]

// 计算方位角
$bearing = Geo::bearing(39.9042, 116.4074, 31.2304, 121.4737); // 121.5度

// 根据方位角和距离计算目标坐标
$target = Geo::destination(39.9042, 116.4074, 121.5, 1000);
// 输出: ['lat' => 39.9135, 'lng' => 116.4183]

// 计算多边形面积（单位：平方千米）
$area = Geo::area([
    [39.9042, 116.4074],
    [39.9142, 116.4174],
    [39.8942, 116.4174],
    [39.8942, 116.3974]
]); // 约0.1平方千米

// 判断点是否在多边形内
$pointInPolygon = Geo::pointInPolygon(39.9042, 116.4074, [
    [39.9042, 116.4074],
    [39.9142, 116.4174],
    [39.8942, 116.4174],
    [39.8942, 116.3974]
]); // true

// 计算多边形周长（单位：千米）
$perimeter = Geo::perimeter([
    [39.9042, 116.4074],
    [39.9142, 116.4174],
    [39.8942, 116.4174],
    [39.8942, 116.3974]
]); // 约0.04千米
```

### IP地址处理使用示例

```php
use Kode\Ip\Ip;

// 获取客户端IP地址
$ip = Ip::getClientIp(); // 192.168.1.1

// 获取客户端IP地址（支持代理）
$ip = Ip::getClientIp(true); // 获取真实IP

// 验证IP地址
$isValidIp = Ip::isValid('192.168.1.1'); // true

// 验证IPv4地址
$isValidIpv4 = Ip::isValidV4('192.168.1.1'); // true

// 验证IPv6地址
$isValidIpv6 = Ip::isValidV6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'); // true

// 判断是否为内网IP
$isPrivate = Ip::isPrivate('192.168.1.1'); // true

// 判断是否为公网IP
$isPublic = Ip::isPublic('8.8.8.8'); // true

// 判断是否为保留IP
$isReserved = Ip::isReserved('127.0.0.1'); // true

// 判断是否为本地回环IP
$isLoopback = Ip::isLoopback('127.0.0.1'); // true

// 判断是否为多播IP
$isMulticast = Ip::isMulticast('224.0.0.1'); // true

// 判断是否为链路本地IP
$isLinkLocal = Ip::isLinkLocal('169.254.1.1'); // true

// 获取IP地址类型
$ipType = Ip::getType('192.168.1.1'); // 'ipv4'

// 获取IP地址版本
$ipVersion = Ip::getVersion('192.168.1.1'); // 4

// 获取IP地址的整数表示
$ipLong = Ip::toLong('192.168.1.1'); // 3232235777

// 从整数表示获取IP地址
$ip = Ip::fromLong(3232235777); // '192.168.1.1'

// 获取IP地址的网络地址
$network = Ip::network('192.168.1.1', 24); // '192.168.1.0'

// 获取IP地址的广播地址
$broadcast = Ip::broadcast('192.168.1.1', 24); // '192.168.1.255'

// 获取IP地址的子网掩码
$netmask = Ip::netmask(24); // '255.255.255.0'

// 判断两个IP地址是否在同一网段
$isSameNetwork = Ip::inSameNetwork('192.168.1.1', '192.168.1.2', 24); // true

// 获取IP地址的CIDR表示
$cidr = Ip::toCidr('192.168.1.0', 24); // '192.168.1.0/24'

// 从CIDR获取网络地址和子网掩码
$cidrInfo = Ip::fromCidr('192.168.1.0/24');
// 输出: ['network' => '192.168.1.0', 'netmask' => '255.255.255.0']

// 获取IP地址的可用范围
$ipRange = Ip::range('192.168.1.0', 24);
// 输出: ['start' => '192.168.1.1', 'end' => '192.168.1.254']

// 获取IP地址的可用数量
$ipCount = Ip::count('192.168.1.0', 24); // 254

// 获取IP地址的地理位置（需要IP地理位置数据库）
$location = Ip::location('8.8.8.8');
// 输出: ['country' => 'United States', 'city' => 'Mountain View', ...]

// 获取IP地址的ISP（需要IP地理位置数据库）
$isp = Ip::isp('8.8.8.8'); // 'Google LLC'
```

### 全局辅助函数使用示例

```php
// 引入全局辅助函数
require __DIR__ . '/vendor/autoload.php';

// 数组辅助函数
$first = arr_first([1, 2, 3, 4, 5]); // 1
$last = arr_last([1, 2, 3, 4, 5]); // 5
$found = arr_find([1, 2, 3, 4, 5], fn($n) => $n > 2); // 3
$foundKey = arr_find_key(['a' => 1, 'b' => 2], fn($n) => $n > 1); // 'b'
$hasAny = arr_any([1, 2, 3, 4, 5], fn($n) => $n > 3); // true
$allMatch = arr_all([1, 2, 3, 4, 5], fn($n) => $n > 0); // true

// 字符串辅助函数
$truncated = str_truncate('这是一段很长的文本内容', 10); // 这是一段很...
$limited = str_limit('这是一段很长的文本内容', 10); // 这是一段很...
$snake = str_snake('helloWorld'); // hello_world
$contains = str_contains('hello world', 'world'); // true
$startsWith = str_starts_with('hello world', 'hello'); // true
$endsWith = str_ends_with('hello world', 'world'); // true
$replaced = str_replace_array('Hello :name', [':name' => '张三']); // Hello 张三
$removed = str_remove('hello world', 'world'); // hello 
$joined = str_join(['a', 'b', 'c'], '-'); // a-b-c
$unique = str_unique('aabbccdd'); // abcd
$shuffled = str_shuffle('abcde'); // 随机打乱
$sub = str_substr('hello world', 0, 5); // hello
$mbSub = str_mb_substr('你好世界', 0, 2); // 你好
$length = str_length('hello'); // 5
$mbLength = str_mb_length('你好'); // 2
$binary = str_to_binary('hello'); // 01101000...
$fromBinary = str_from_binary('01101000...'); // hello
$hex = str_to_hex('hello'); // 68656c6c6f
$fromHex = str_from_hex('68656c6c6f'); // hello
$base64 = str_to_base64('hello'); // aGVsbG8=
$fromBase64 = str_from_base64('aGVsbG8='); // hello
$urlEncoded = str_to_url_encode('hello world'); // hello%20world
$urlDecoded = str_from_url_decode('hello%20world'); // hello world
$compressed = str_compress('这是一段需要压缩的文本内容');
$decompressed = str_decompress($compressed);

// 时间辅助函数
$now = time_now(); // 当前时间戳
$today = time_today(); // 今天日期
$yesterday = time_yesterday(); // 昨天日期
$tomorrow = time_tomorrow(); // 明天日期
$diff = time_diff($start, $end); // 时间差
$format = time_format(time()); // 格式化时间

// 地理位置辅助函数
$distance = geo_distance(39.9042, 116.4074, 31.2304, 121.4737); // 距离
$isValidLng = geo_is_valid_lng(116.4074); // 验证经度
$isValidLat = geo_is_valid_lat(39.9042); // 验证纬度

// IP地址辅助函数
$ip = ip_get_client_ip(); // 获取客户端IP
$isValidIp = ip_is_valid('192.168.1.1'); // 验证IP
$isPrivate = ip_is_private('192.168.1.1'); // 是否为内网IP
$isPublic = ip_is_public('8.8.8.8'); // 是否为公网IP

// 消息体辅助函数
$result = msg_result(200, '操作成功', ['id' => 123]); // 返回结果数组
$json = msg_json(200, '操作成功', ['id' => 123]); // 返回JSON字符串

// 加解密辅助函数
$encrypted = crypto_encrypt('敏感数据'); // 加密
$decrypted = crypto_decrypt($encrypted); // 解密
$orderNo = crypto_order('ORD'); // 生成订单号
$inviteCode = crypto_invite(6); // 生成邀请码
$urlSafeCode = crypto_url(16); // 生成URL安全码
$regCode = crypto_reg(16, 4, '-'); // 生成注册码

// 数学辅助函数
$sum = math_sum([1, 2, 3, 4, 5]); // 求和
$avg = math_avg([1, 2, 3, 4, 5]); // 平均值
$round = math_round(3.14159, 2); // 四舍五入
$ceil = math_ceil(3.2); // 向上取整
$floor = math_floor(3.8); // 向下取整
$max = math_max([1, 2, 3, 4, 5]); // 最大值
$min = math_min([1, 2, 3, 4, 5]); // 最小值
$random = math_random(1, 100); // 随机数
```

```php
use Kode\Time\Time;

// 时间格式化
$now = Time::now(); // 2025-07-01 12:34:56
$formatted = Time::format(time(), 'Y-m-d'); // 2025-07-01

// 时间计算
$tomorrow = Time::add(time(), 86400);
$yesterday = Time::sub(time(), 86400);
$diff = Time::diff($start, $end);

// 常用时间获取
$today = Time::today(); // 2025-07-01
$yesterday = Time::yesterday(); // 2025-06-30
$tomorrow = Time::tomorrow(); // 2025-07-02

// 本周时间
$thisWeek = Time::thisWeek(); // ['start' => '2025-06-30', 'end' => '2025-07-06']

// 本月时间
$thisMonth = Time::thisMonth(); // ['start' => '2025-07-01', 'end' => '2025-07-31']

// 本季度时间
$thisQuarter = Time::thisQuarter(); // ['start' => '2025-07-01', 'end' => '2025-09-30']

// 本年时间
$thisYear = Time::thisYear(); // ['start' => '2025-01-01', 'end' => '2025-12-31']

// 上周时间
$lastWeek = Time::lastWeek(); // ['start' => '2025-06-23', 'end' => '2025-06-29']

// 上月时间
$lastMonth = Time::lastMonth(); // ['start' => '2025-06-01', 'end' => '2025-06-30']

// 上季度时间
$lastQuarter = Time::lastQuarter(); // ['start' => '2025-04-01', 'end' => '2025-06-30']

// 去年时间
$lastYear = Time::lastYear(); // ['start' => '2024-01-01', 'end' => '2024-12-31']

// 获取日期范围
$dateRange = Time::getDateRange('2025-01-01', '2025-01-31'); // ['2025-01-01', '2025-01-02', ..., '2025-01-31']

// 获取月份日期
$monthDates = Time::getMonthDates(2025, 1); // ['2025-01-01', '2025-01-02', ..., '2025-01-31']

// 获取季度日期
$quarterDates = Time::getQuarterDates(2025, 1); // ['2025-01-01', '2025-01-02', ..., '2025-03-31']

// 获取年份日期
$yearDates = Time::getYearDates(2025); // ['2025-01-01', '2025-01-02', ..., '2025-12-31']

// 人性化时间差
$humanDiff = Time::humanDiff(time() - 3600); // 1小时前

// 时间戳转日期
$toDate = Time::toDate(time()); // 2025-07-01

// 时间戳转时间
$toTime = Time::toTime(time()); // 12:34:56

// 时间戳转日期时间
$toDateTime = Time::toDateTime(time()); // 2025-07-01 12:34:56

// 日期转时间戳
$toTimestamp = Time::toTimestamp('2025-07-01 12:34:56'); // 1719815696

// 判断是否为今天
$isToday = Time::isToday(time()); // true

// 判断是否为昨天
$isYesterday = Time::isYesterday(time() - 86400); // true

// 判断是否为本周
$isThisWeek = Time::isThisWeek(time()); // true

// 判断是否为本月
$isThisMonth = Time::isThisMonth(time()); // true

// 判断是否为本年
$isThisYear = Time::isThisYear(time()); // true

// 获取星期几
$dayOfWeek = Time::dayOfWeek(time()); // 1（周一）

// 获取星期几名称
$dayOfWeekName = Time::dayOfWeekName(time()); // 周一

// 获取月份名称
$monthName = Time::monthName(time()); // 七月

// 获取季度
$quarter = Time::quarter(time()); // 3

// 获取季度名称
$quarterName = Time::quarterName(time()); // 第三季度

// 获取年份
$year = Time::year(time()); // 2025

// 获取月份
$month = Time::month(time()); // 7

// 获取日期
$day = Time::day(time()); // 1

// 获取小时
$hour = Time::hour(time()); // 12

// 获取分钟
$minute = Time::minute(time()); // 34

// 获取秒数
$second = Time::second(time()); // 56

// 获取月份天数
$daysInMonth = Time::daysInMonth(2025, 7); // 31

// 获取季度天数
$daysInQuarter = Time::daysInQuarter(2025, 3); // 92

// 获取年份天数
$daysInYear = Time::daysInYear(2025); // 365

// 判断是否为闰年
$isLeapYear = Time::isLeapYear(2025); // false

// 判断是否为工作日
$isWorkday = Time::isWorkday(time()); // true

// 判断是否为周末
$isWeekend = Time::isWeekend(time()); // false

// 获取工作日
$workdays = Time::getWorkdays('2025-07-01', '2025-07-31'); // ['2025-07-01', '2025-07-02', ..., '2025-07-31']

// 获取周末日期
$weekends = Time::getWeekends('2025-07-01', '2025-07-31'); // ['2025-07-05', '2025-07-06', ..., '2025-07-26', '2025-07-27']

// 计算工作日
$workdayCount = Time::countWorkdays('2025-07-01', '2025-07-31'); // 23

// 计算周末天数
$weekendCount = Time::countWeekends('2025-07-01', '2025-07-31'); // 8

// 计算两个日期之间的天数
$daysBetween = Time::daysBetween('2025-07-01', '2025-07-31'); // 30

// 计算两个日期之间的工作日
$workdaysBetween = Time::workdaysBetween('2025-07-01', '2025-07-31'); // 23

// 计算两个日期之间的周末天数
$weekendsBetween = Time::weekendsBetween('2025-07-01', '2025-07-31'); // 8

// 计算两个日期之间的月数
$monthsBetween = Time::monthsBetween('2025-01-01', '2025-07-01'); // 6

// 计算两个日期之间的年数
$yearsBetween = Time::yearsBetween('2020-01-01', '2025-01-01'); // 5

// 计算两个日期之间的季度数
$quartersBetween = Time::quartersBetween('2025-01-01', '2025-07-01'); // 2

// 计算两个日期之间的周数
$weeksBetween = Time::weeksBetween('2025-07-01', '2025-07-31'); // 4

// 计算两个日期之间的小时数
$hoursBetween = Time::hoursBetween('2025-07-01 00:00:00', '2025-07-01 12:00:00'); // 12

// 计算两个日期之间的分钟数
$minutesBetween = Time::minutesBetween('2025-07-01 00:00:00', '2025-07-01 01:00:00'); // 60

// 计算两个日期之间的秒数
$secondsBetween = Time::secondsBetween('2025-07-01 00:00:00', '2025-07-01 01:00:00'); // 3600

// 获取月份第一天
$firstDayOfMonth = Time::firstDayOfMonth(2025, 7); // 2025-07-01

// 获取月份最后一天
$lastDayOfMonth = Time::lastDayOfMonth(2025, 7); // 2025-07-31

// 获取季度第一天
$firstDayOfQuarter = Time::firstDayOfQuarter(2025, 3); // 2025-07-01

// 获取季度最后一天
$lastDayOfQuarter = Time::lastDayOfQuarter(2025, 3); // 2025-09-30

// 获取年份第一天
$firstDayOfYear = Time::firstDayOfYear(2025); // 2025-01-01

// 获取年份最后一天
$lastDayOfYear = Time::lastDayOfYear(2025); // 2025-12-31

// 获取周第一天
$firstDayOfWeek = Time::firstDayOfWeek(time()); // 2025-06-30

// 获取周最后一天
$lastDayOfWeek = Time::lastDayOfWeek(time()); // 2025-07-06

// 获取下个月
$nextMonth = Time::nextMonth(time()); // 2025-08

// 获取上个月
$prevMonth = Time::prevMonth(time()); // 2025-06

// 获取明年
$nextYear = Time::nextYear(time()); // 2026

// 获取去年
$prevYear = Time::prevYear(time()); // 2024

// 获取下个季度
$nextQuarter = Time::nextQuarter(time()); // 2025-Q4

// 获取上个季度
$prevQuarter = Time::prevQuarter(time()); // 2025-Q2
```

### 消息体使用示例

#### 对象调用

```php
use Kode\Message\Message;

$msg = new Message();
$result = $msg->code(200)
              ->msg('操作成功')
              ->data(['id' => 123, 'name' => '测试'])
              ->total(200)
              ->time('2027-10-22 10:10:10')
              ->result();
```

#### 静态调用

```php
use Kode\Message\Message;

$result = Message::code(302)
                 ->msg('错啦')
                 ->total(200)
                 ->time('2027-10-22 10:10:10')
                 ->result();
```

#### JSON输出

```php
$json = Message::code(200)->msg('操作成功')->json();
```

### 字段转换配置

```php
// 设置全局字段转换
Message::setGlobalFieldTransform([
    'code' => 'codes',
    'msg' => 'message',
    'data' => 'result'
]);

// 输出结果将包含 codes/message/result 字段
$result = Message::code(200)->msg('操作成功')->result();
```

### 自定义状态码

#### 动态添加

```php
$msg = new Message();
$msg->addCode(800000, '自定义业务异常')
    ->addCode(900000, '权限不足');
```

#### 从文件加载

```php
// 自定义状态码文件 custom_codes.php
return [
    800000 => '自定义业务异常',
    900000 => '权限不足'
];

// 加载自定义状态码文件
$msg = new Message(customCodeFile: 'custom_codes.php');

// 或者动态设置
$msg->setCustomCodeFile('custom_codes.php');

// 重新加载文件
$msg->reloadCustomCodeFile();
```

### 加解密模块使用示例

加解密模块提供了三种加密引擎（Sodium、OpenSSL、自动选择）和三种加密模式（标准、URL安全、紧凑），支持AES-256-GCM高级加密标准。

#### 加密引擎说明

```php
use Kode\Crypto\Crypto;

// Sodium引擎 - 推荐使用（性能更高，安全性更强）
// 需要PHP扩展：sodium
$crypto = new Crypto('your_key', Crypto::ENGINE_SODIUM);

// OpenSSL引擎 - 通用选择
// 需要PHP扩展：openssl
$crypto = new Crypto('your_key', Crypto::ENGINE_OPENSSL);

// 自动选择引擎 - 自动选择最优引擎
$crypto = new Crypto('your_key', Crypto::ENGINE_AUTO);
```

#### 加密模式说明

```php
use Kode\Crypto\Crypto;

// 标准模式 - Base64编码
$crypto = new Crypto('your_key', Crypto::ENGINE_AUTO, Crypto::MODE_STANDARD);
$encrypted = $crypto->encrypt('敏感数据');
// 输出示例: VEhJUz1mYWxzZVZlcnNpb249MS4wJmtleT1zZWN1cmVfazEyMw==

// URL安全模式 - Base64URL编码（无=号，适合URL传输）
$crypto = new Crypto('your_key', Crypto::ENGINE_AUTO, Crypto::MODE_URL_SAFE);
$encrypted = $crypto->encrypt('敏感数据');
// 输出示例: VEhJUz1mYWxzZVZlcnNpb249MS4wJmtleT1zZWN1cmVfazEyMw

// 紧凑模式 - 十六进制编码（最短长度，适合存储）
$crypto = new Crypto('your_key', Crypto::ENGINE_AUTO, Crypto::MODE_COMPACT);
$encrypted = $crypto->encrypt('敏感数据');
// 输出示例: 54484349533b66756c73652076657273696f6e20312e302e303b6b65793d7365637572655f6b313233
```

#### 基础加解密

```php
use Kode\Crypto\Crypto;

// 创建加密实例
$crypto = new Crypto('your_secret_key_2025');

// 加密数据
$encrypted = $crypto->encrypt('这是需要加密的敏感数据');
echo $encrypted;
// 输出示例: VEhJUz1mYWxzZVZlcnNpb249MS4wJmtleT1zZWN1cmVfazEyMw==

// 解密数据
$decrypted = $crypto->decrypt($encrypted);
echo $decrypted;
// 输出: 这是需要加密的敏感数据

// 使用不同的密钥
$crypto2 = new Crypto('another_key');
$encrypted2 = $crypto2->encrypt('另一个敏感数据');

// 解密（需要使用相同的密钥）
$decrypted2 = $crypto2->decrypt($encrypted2);
```

#### 静态方法调用

```php
use Kode\Crypto\Crypto;

// 静态加密（使用默认密钥）
$encrypted = Crypto::encrypt('敏感数据');
$decrypted = Crypto::decrypt($encrypted);

// 使用自定义密钥的静态方法
$encrypted = (new Crypto('custom_key'))->encrypt('敏感数据');
$decrypted = (new Crypto('custom_key'))->decrypt($encrypted);
```

#### 密码哈希与验证

```php
use Kode\Crypto\Crypto;

// 生成密码哈希
$password = 'my_secure_password';
$hash = Crypto::passwordHash($password);
echo $hash;
// 输出示例: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

// 验证密码
$isValid = Crypto::passwordVerify('my_secure_password', $hash);
echo $isValid ? '密码正确' : '密码错误'; // 密码正确

$isValid = Crypto::passwordVerify('wrong_password', $hash);
echo $isValid ? '密码正确' : '密码错误'; // 密码错误

// 密码哈希更新（重新哈希）
if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
    $newHash = Crypto::passwordHash($password);
}
```

#### MD5加密（支持加盐）

```php
use Kode\Crypto\Crypto;

// 基础MD5加密
$md5 = Crypto::md5('123456');
echo $md5;
// 输出: e10adc3949ba59abbe56e057f20f883e

// 加盐MD5加密
$salt = 'your_custom_salt';
$md5WithSalt = Crypto::md5('123456', $salt);
echo $md5WithSalt;
// 输出: 52c69e3a57331081823331c4e6999d23

// 多次加盐（提高安全性）
$doubleSalt = Crypto::md5(Crypto::md5('123456'), $salt);
```

#### HMAC签名

```php
use Kode\Crypto\Crypto;

// SHA256签名（默认）
$signature = Crypto::hmac('待签名数据', 'your_secret_key');
echo $signature;
// 输出示例: 3a6eb0790f39ac87c94f3856b2dd2c5d110e0f9b0e9c9d6e7b8c9d0e1f2a3b4c

// SHA512签名
$signature512 = Crypto::hmac('待签名数据', 'your_secret_key', 'sha512');
echo $signature512;
// 输出示例: a4e6b8c0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2

// MD5签名
$signatureMd5 = Crypto::hmac('待签名数据', 'your_secret_key', 'md5');
echo $signatureMd5;
// 输出示例: 1a1dc06f7a0b2c8d9e0f1a2b3c4d5e6f7

// 数据完整性验证
$originalData = '订单数据';
$originalSignature = Crypto::hmac($originalData, 'api_secret');

$receivedData = '订单数据';
$receivedSignature = $_SERVER['HTTP_SIGNATURE'] ?? '';

if (hash_equals($originalSignature, $receivedSignature)) {
    echo '数据完整性验证通过';
} else {
    echo '数据可能被篡改';
}
```

#### SSL对称加解密

```php
use Kode\Crypto\Crypto;

// 使用自定义密钥进行SSL加密
$key = 'your_ssl_key_32_bytes_long!';
$crypto = new Crypto();

$encrypted = $crypto->sslEncrypt('SSL加密数据', $key);
echo $encrypted;
// 输出示例: VGhpc0lzU1NMY0VuY3J5cHRlZERhdGE=

$decrypted = $crypto->sslDecrypt($encrypted, $key);
echo $decrypted;
// 输出: SSL加密数据
```

#### 错误处理

```php
use Kode\Crypto\Crypto;

$crypto = new Crypto('your_key');

try {
    $encrypted = $crypto->encrypt('敏感数据');
    $decrypted = $crypto->decrypt($encrypted);
    echo '加解密成功: ' . $decrypted;
} catch (\Exception $e) {
    echo '加解密失败: ' . $e->getMessage();
}

// 密钥错误时的解密异常
try {
    $wrongCrypto = new Crypto('wrong_key');
    $decrypted = $wrongCrypto->decrypt($encrypted);
} catch (\Exception $e) {
    echo '解密失败（密钥错误）: ' . $e->getMessage();
}
```

#### 使用场景示例

```php
use Kode\Crypto\Crypto;

// 场景1：用户敏感信息加密存储
function saveUserSensitiveData(Crypto $crypto, array $data): array
{
    return [
        'id' => $data['id'],
        'name' => $data['name'],
        'encrypted_phone' => $crypto->encrypt($data['phone']),
        'encrypted_id_card' => $crypto->encrypt($data['id_card'])
    ];
}

function getUserSensitiveData(Crypto $crypto, array $userData): array
{
    return [
        'id' => $userData['id'],
        'name' => $userData['name'],
        'phone' => $crypto->decrypt($userData['encrypted_phone']),
        'id_card' => $crypto->decrypt($userData['encrypted_id_card'])
    ];
}

// 场景2：API请求签名验证
function verifyApiRequest(Crypto $crypto, array $params, string $signature): bool
{
    $expectedSignature = Crypto::hmac(json_encode($params), $apiSecret);
    return hash_equals($expectedSignature, $signature);
}

// 场景3：密码安全存储
function registerUser(string $password): string
{
    return Crypto::passwordHash($password);
}

function verifyUserPassword(string $password, string $hash): bool
{
    return Crypto::passwordVerify($password, $hash);
}
```

### 代码生成使用示例

代码生成模块提供了订单号、邀请码、URL安全码、注册码等常用代码的生成功能。

#### 订单号生成

```php
use Kode\Crypto\Crypto;

// 生成带前缀的订单号
$orderNo = Crypto::order('ORD');
echo $orderNo;
// 输出示例: ORD202507261234561234

// 生成不带前缀的订单号
$orderNo = Crypto::order('');
echo $orderNo;
// 输出示例: 202507261234561234

// 实际应用场景
function generateOrderNo(string $prefix = 'ORDER'): string
{
    return Crypto::order($prefix);
}

// 使用示例
$orderNo = generateOrderNo('ORD');
// 输出: ORD202507261234561234

$orderNo = generateOrderNo('REFUND');
// 输出: REFUND202507261234561234
```

#### 邀请码生成

```php
use Kode\Crypto\Crypto;

// 生成6位邀请码（默认使用字母+数字）
$inviteCode = Crypto::invite(6);
echo $inviteCode;
// 输出示例: A1B2C3

// 生成8位邀请码
$inviteCode = Crypto::invite(8);
echo $inviteCode;
// 输出示例: A1B2C3D4

// 仅使用字母
$inviteCode = Crypto::invite(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
echo $inviteCode;
// 输出示例: ABCDEF

// 仅使用数字
$inviteCode = Crypto::invite(6, '0123456789');
echo $inviteCode;
// 输出示例: 123456

// 自定义字符集
$inviteCode = Crypto::invite(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
echo $inviteCode;
// 输出示例: AbCdEfGh

// 实际应用场景
function generateInviteCode(int $length = 6): string
{
    return Crypto::invite($length);
}

// 用户注册邀请码
$userInviteCode = generateInviteCode(8);
// 输出: A1B2C3D4

// 活动邀请码（仅大写字母）
$eventInviteCode = Crypto::invite(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
// 输出: ABC123
```

#### URL安全码生成

```php
use Kode\Crypto\Crypto;

// 生成16位URL安全码（默认）
$urlCode = Crypto::url();
echo $urlCode;
// 输出示例: abcdefghijklmnop

// 生成32位URL安全码
$urlCode = Crypto::url(32);
echo $urlCode;
// 输出示例: abcdefghijklmnopqrstuvwxyz123456

// 生成64位URL安全码
$urlCode = Crypto::url(64);
echo $urlCode;
// 输出示例: abcdefghijklmnopqrstuvwxyz123456ABCDEFGHIJKLMNOPQRSTUVWXYZabcdef

// 实际应用场景
function generateSecureToken(int $length = 32): string
{
    return Crypto::url($length);
}

// 生成API密钥
$apiKey = generateSecureToken(64);

// 生成密码重置令牌
$resetToken = generateSecureToken(32);

// 生成会话ID
$sessionId = generateSecureToken(16);
```

#### 注册码生成

```php
use Kode\Crypto\Crypto;

// 生成16位注册码，4位分段（默认）
$regCode = Crypto::reg();
echo $regCode;
// 输出示例: ABCD-EFGH-IJKL-MNOP

// 生成12位注册码，4位分段
$regCode = Crypto::reg(12);
echo $regCode;
// 输出示例: ABCD-EFGH-IJKL

// 生成20位注册码，5位分段
$regCode = Crypto::reg(20, 5);
echo $regCode;
// 输出示例: ABCDE-FGHij-KL123-mNOPQ-Rstuv

// 不使用分隔符
$regCode = Crypto::reg(16, 16, '');
echo $regCode;
// 输出示例: ABCDEFGHIJKLMNOP

// 自定义分隔符
$regCode = Crypto::reg(12, 4, '|');
echo $regCode;
// 输出示例: ABCD|EFGH|IJKL

// 实际应用场景
function generateLicenseKey(string $product = 'PRO', int $length = 16): string
{
    return $product . '-' . Crypto::reg($length);
}

// 生成软件许可证密钥
$licenseKey = generateLicenseKey('PRO');
// 输出示例: PRO-ABCD-EFGH-IJKL-MNOP

// 生成激活码
$activationCode = Crypto::reg(12, 4, '-');
// 输出示例: ABCD-EFGH-IJKL

// 生成优惠券码
$couponCode = Crypto::reg(10, 5, '');
// 输出示例: ABCDE12345
```

#### 随机数生成

```php
use Kode\Crypto\Crypto;

// 生成指定范围的随机整数
$random = Crypto::random(1, 100);
echo $random;
// 输出示例: 42

$random = Crypto::random(1000, 9999);
echo $random;
// 输出示例: 5678

// 生成随机字符串
$randomStr = Crypto::randomStr(16);
echo $randomStr;
// 输出示例: aB3dE7fG9jK2mN6

// 实际应用场景
function generateVerificationCode(): string
{
    return Crypto::random(100000, 999999);
}

// 生成6位数字验证码
$verificationCode = generateVerificationCode();
// 输出示例: 123456

// 生成4位数字PIN码
$pinCode = Crypto::random(1000, 9999);
// 输出示例: 5678

// 生成随机密码
$randomPassword = Crypto::randomStr(12);
// 输出示例: xK9mP2vL5nR8
```



#### 数组转路径结构



```php
// 数组转路径结构
$pathList = Arr::path($list);
// 输出示例:
// [
//     ['id' => 1, 'parent_id' => 0, 'name' => '根节点', 'path' => '1'],
//     ['id' => 2, 'parent_id' => 1, 'name' => '子节点1', 'path' => '1,2'],
//     ['id' => 3, 'parent_id' => 1, 'name' => '子节点2', 'path' => '1,3'],
//     ['id' => 4, 'parent_id' => 2, 'name' => '孙节点1', 'path' => '1,2,4']
// ]
```

## 高级特性

### 进程和协程支持

工具包采用线程安全的初始化机制，确保在多进程和协程环境下的稳定运行。

### 魔术方法扩展

通过魔术方法可以动态设置任意字段：

```php
$result = Message::code(200)
                 ->msg('操作成功')
                 ->total(200)
                 ->page(1)
                 ->size(10)
                 ->traceId('abc123')
                 ->result();
```

### 空数据处理

当data字段为空时，不会在结果中显示：

```php
// 结果中不包含data字段
$result = Message::code(200)->msg('操作成功')->result();

// 结果中包含data字段（空数组）
$result = Message::code(200)->msg('操作成功')->data([])->result();
```

## 核心API

### 消息体核心方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `code(int $code)` | 设置状态码 | $code: 状态码 |
| `msg(string $msg)` | 设置消息内容 | $msg: 消息文本 |
| `data(mixed $data)` | 设置业务数据 | $data: 业务数据 |
| `ext(string $key, mixed $value)` | 添加自定义字段 | $key: 字段名, $value: 字段值 |
| `setFieldTransform(array $config)` | 设置字段转换配置 | $config: 字段映射数组 |
| `setCustomCodeFile(string $filePath)` | 设置自定义状态码文件 | $filePath: 文件路径 |
| `reloadCustomCodeFile()` | 重新加载自定义状态码文件 | - |
| `result()` | 输出结果数组 | - |
| `json()` | 输出JSON字符串 | - |

### 加解密核心方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `encrypt(string $data)` | 加密数据 | $data: 待加密数据 |
| `decrypt(string $data)` | 解密数据 | $data: 待解密数据 |
| `setEngine(string $engine)` | 设置加密引擎 | $engine: 加密引擎（auto/sodium/openssl） |
| `setMode(string $mode)` | 设置加密模式 | $mode: 加密模式（standard/url_safe/compact） |

### 代码生成核心方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `order(string $prefix = '')` | 生成订单号 | $prefix: 前缀 |
| `invite(int $length = 6, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')` | 生成邀请码 | $length: 长度, $chars: 字符集 |
| `url(int $length = 16)` | 生成URL安全码 | $length: 长度 |
| `reg(int $length = 16, int $segmentLength = 4, string $separator = '-')` | 生成注册码 | $length: 长度, $segmentLength: 分段长度, $separator: 分隔符 |

### 树形结构处理方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `tree(array $array, string $idKey, string $pidKey, string $childrenKey)` | 数组转树形结构 | $array: 扁平数组, $idKey: ID键名, $pidKey: 父ID键名, $childrenKey: 子节点键名 |
| `list(array $tree, string $childrenKey)` | 树形结构转数组 | $tree: 树形数组, $childrenKey: 子节点键名 |
| `level(array $array, string $idKey, string $pidKey, string $levelKey)` | 数组转层级结构 | $array: 扁平数组, $idKey: ID键名, $pidKey: 父ID键名, $levelKey: 层级键名 |
| `path(array $array, string $idKey, string $pidKey, string $nameKey, string $pathKey, string $separator)` | 数组转路径结构 | $array: 扁平数组, $idKey: ID键名, $pidKey: 父ID键名, $nameKey: 名称键名, $pathKey: 路径键名, $separator: 分隔符 |

### 数组处理核心方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `first(array $array)` | 获取数组第一个元素 | $array: 数组 |
| `last(array $array)` | 获取数组最后一个元素 | $array: 数组 |
| `find(array $array, callable $callback)` | 查找满足条件的元素 | $array: 数组, $callback: 回调函数 |
| `findKey(array $array, callable $callback)` | 查找满足条件的键名 | $array: 数组, $callback: 回调函数 |
| `any(array $array, callable $callback)` | 检查是否存在满足条件的元素 | $array: 数组, $callback: 回调函数 |
| `all(array $array, callable $callback)` | 检查是否所有元素都满足条件 | $array: 数组, $callback: 回调函数 |
| `get(array $array, string|array $key, mixed $default)` | 获取数组值（支持点语法） | $array: 数组, $key: 键名, $default: 默认值 |
| `set(array $array, string|array $key, mixed $value)` | 设置数组值 | $array: 数组, $key: 键名, $value: 值 |
| `has(array $array, string|array $key)` | 判断是否存在键 | $array: 数组, $key: 键名 |
| `only(array $array, array $keys)` | 仅保留指定键 | $array: 数组, $keys: 键名数组 |
| `except(array $array, array $keys)` | 排除指定键 | $array: 数组, $keys: 键名数组 |
| `deepMerge(array ...$arrays)` | 数组深度合并 | $arrays: 多个数组 |
| `group(array $array, string $key)` | 多维数组分组 | $array: 数组, $key: 分组键名 |
| `count(array $array, string $key)` | 多维数组统计 | $array: 数组, $key: 统计键名 |
| `sum(array $array, string $key)` | 多维数组求和 | $array: 数组, $key: 求和键名 |
| `avg(array $array, string $key)` | 多维数组求平均值 | $array: 数组, $key: 平均键名 |
| `max(array $array, string $key)` | 多维数组求最大值 | $array: 数组, $key: 最大值键名 |
| `min(array $array, string $key)` | 多维数组求最小值 | $array: 数组, $key: 最小值键名 |

### 字符串处理核心方法

| 方法名 | 功能描述 | 参数说明 |
|--------|----------|----------|
| `mask(string $str, int $start, int $length, string $mask)` | 字符串脱敏 | $str: 字符串, $start: 开始位置, $length: 长度, $mask: 掩码字符 |
| `maskPhone(string $phone, int $start, int $length)` | 手机号脱敏 | $phone: 手机号, $start: 开始位置, $length: 长度 |
| `maskEmail(string $email, int $start, int $length)` | 邮箱脱敏 | $email: 邮箱, $start: 开始位置, $length: 长度 |
| `maskIdCard(string $idCard, int $start, int $length)` | 身份证号脱敏 | $idCard: 身份证号, $start: 开始位置, $length: 长度 |
| `maskBankCard(string $bankCard, int $start, int $length)` | 银行卡号脱敏 | $bankCard: 银行卡号, $start: 开始位置, $length: 长度 |
| `maskName(string $name, int $start, int $length)` | 姓名脱敏 | $name: 姓名, $start: 开始位置, $length: 长度 |
| `length(string $str)` | 字符串长度 | $str: 字符串 |
| `truncate(string $str, int $length, string $suffix)` | 字符串截断 | $str: 字符串, $length: 截断长度, $suffix: 后缀 |
| `limit(string $str, int $length, string $suffix)` | 字符串限制长度 | $str: 字符串, $length: 限制长度, $suffix: 后缀 |
| `snake(string $str)` | 驼峰转下划线 | $str: 字符串 |
| `camel(string $str)` | 下划线转驼峰 | $str: 字符串 |
| `pascal(string $str)` | 下划线转大驼峰 | $str: 字符串 |
| `kebab(string $str)` | 驼峰转短横线 | $str: 字符串 |
| `random(int $length)` | 生成随机字符串 | $length: 长度 |
| `uuid()` | 生成UUID | - |
| `contains(string $str, string $needle)` | 是否包含子串 | $str: 字符串, $needle: 子串 |
| `startsWith(string $str, string $needle)` | 是否以子串开头 | $str: 字符串, $needle: 子串 |
| `endsWith(string $str, string $needle)` | 是否以子串结尾 | $str: 字符串, $needle: 子串 |
| `toBase64(string $str)` | 转Base64编码 | $str: 字符串 |
| `fromBase64(string $str)` | Base64解码 | $str: Base64字符串 |
| `toJson(mixed $data)` | 转JSON | $data: 数据 |
| `fromJson(string $json)` | JSON解码 | $json: JSON字符串 |
| `toXml(array $data, string $root)` | 转XML | $data: 数据, $root: 根节点名 |
| `fromXml(string $xml)` | XML解码 | $xml: XML字符串 |
| `toArray(string $str, string $delimiter)` | 转数组 | $str: 字符串, $delimiter: 分隔符 |
| `fromArray(array $array, string $delimiter)` | 数组转字符串 | $array: 数组, $delimiter: 分隔符 |

## 数学计算模块使用示例

数学计算模块提供了高精度数学计算、金融计算、统计分析等功能，使用BCMath扩展解决浮点数精度丢失问题。

### 基础运算

```php
use Kode\Math\Math;

// 加法运算
$result = Math::add('1.1', '2.2'); // 3.3
$result = Math::add('1.1', '2.2', 4); // 3.3000（保留4位小数）

// 减法运算
$result = Math::sub('5.5', '2.2'); // 3.3
$result = Math::sub('5.5', '2.2', 4); // 3.3000（保留4位小数）

// 乘法运算
$result = Math::mul('1.5', '2'); // 3
$result = Math::mul('1.5', '2', 4); // 3.0000（保留4位小数）

// 除法运算
$result = Math::div('10', '3'); // 3.3333333333
$result = Math::div('10', '3', 2); // 3.33（保留2位小数）

// 取模运算
$result = Math::mod('10', '3'); // 1

// 幂运算
$result = Math::pow('2', '10'); // 1024
$result = Math::pow('2', '10', 4); // 1024.0000（保留4位小数）

// 平方根运算
$result = Math::sqrt('16'); // 4
$result = Math::sqrt('2', 10); // 1.4142135624（保留10位小数）
```

### 数值处理

```php
use Kode\Math\Math;

// 四舍五入
$result = Math::round('3.14159', 2); // 3.14
$result = Math::round('3.14159', 4); // 3.1416

// 向上取整
$result = Math::ceil('3.2'); // 4
$result = Math::ceil('3.8'); // 4
$result = Math::ceil('3.14159', 2); // 3.15（保留2位小数后向上取整）

// 向下取整
$result = Math::floor('3.2'); // 3
$result = Math::floor('3.8'); // 3
$result = Math::floor('3.14159', 2); // 3.14（保留2位小数后向下取整）

// 绝对值
$result = Math::abs('-5'); // 5
$result = Math::abs('5'); // 5

// 数值比较
$result = Math::compare('5', '3'); // 1（5 > 3）
$result = Math::compare('3', '5'); // -1（3 < 5）
$result = Math::compare('5', '5'); // 0（5 == 5）

// 判断是否相等
$result = Math::equal('1.1', '1.10', 2); // true（保留2位小数比较）
$result = Math::equal('1.1', '1.10', 10); // false（保留10位小数比较）

// 格式化数字
$result = Math::format('1234.5678', 2); // 1,234.57（千分位分隔符）
$result = Math::format('1234.5678', 2, false); // 1234.57（无千分位分隔符）
```

### 三角函数

```php
use Kode\Math\Math;

// 正弦函数
$result = Math::sin(deg2rad(30)); // 0.5（30度的正弦值）
$result = Math::sin(deg2rad(30), 10); // 0.5000000000（保留10位小数）

// 余弦函数
$result = Math::cos(deg2rad(60)); // 0.5（60度的余弦值）
$result = Math::cos(deg2rad(60), 10); // 0.5000000000（保留10位小数）

// 正切函数
$result = Math::tan(deg2rad(45)); // 1（45度的正切值）
$result = Math::tan(deg2rad(45), 10); // 1.0000000000（保留10位小数）

// 反正弦函数
$result = Math::asin(0.5); // 0.5235987756（弧度）
$result = Math::asin(0.5, 10); // 0.5235987756（保留10位小数）

// 反余弦函数
$result = Math::acos(0.5); // 1.0471975512（弧度）
$result = Math::acos(0.5, 10); // 1.0471975512（保留10位小数）

// 反正切函数
$result = Math::atan(1); // 0.7853981634（弧度）
$result = Math::atan(1, 10); // 0.7853981634（保留10位小数）

// 弧度转角度
$result = Math::rad2deg(deg2rad(90)); // 90（弧度转角度）
$result = Math::rad2deg(deg2rad(90), 10); // 90.0000000000（保留10位小数）

// 角度转弧度
$result = Math::deg2rad(90); // 1.5707963268（角度转弧度）
$result = Math::deg2rad(90, 10); // 1.5707963268（保留10位小数）
```

### 对数函数

```php
use Kode\Math\Math;

// 自然对数
$result = Math::ln('10'); // 2.3025850930
$result = Math::ln('10', 10); // 2.3025850930（保留10位小数）

// 常用对数
$result = Math::log10('100'); // 2
$result = Math::log10('100', 10); // 2.0000000000（保留10位小数）

// 自定义底数对数
$result = Math::log('8', '2'); // 3（以2为底8的对数）
$result = Math::log('8', '2', 10); // 3.0000000000（保留10位小数）
```

### 数论函数

```php
use Kode\Math\Math;

// 阶乘
$result = Math::factorial(5); // 120（5! = 5×4×3×2×1）
$result = Math::factorial(0); // 1（0! = 1）

// 最大公约数
$result = Math::gcd(12, 18); // 6（12和18的最大公约数）
$result = Math::gcd(24, 36); // 12（24和36的最大公约数）

// 最小公倍数
$result = Math::lcm(12, 18); // 36（12和18的最小公倍数）
$result = Math::lcm(24, 36); // 72（24和36的最小公倍数）
```

### 金融计算

```php
use Kode\Math\Math;

// 百分比计算
$result = Math::percentage('25', '100'); // 25（25占100的百分比）
$result = Math::percentage('25', '100', 4); // 25.0000（保留4位小数）

// 折扣计算
$result = Math::discount('100', '0.8'); // 80（100元打8折）
$result = Math::discount('100', '0.8', 4); // 80.0000（保留4位小数）

// 税费计算
$result = Math::tax('100', '0.13'); // 13（100元的13%税费）
$result = Math::tax('100', '0.13', 4); // 13.0000（保留4位小数）

// 含税金额计算
$result = Math::taxIncluded('100', '0.13'); // 113（不含税100元，税率13%，含税113元）
$result = Math::taxIncluded('100', '0.13', 4); // 113.0000（保留4位小数）

// 不含税金额计算
$result = Math::taxExcluded('113', '0.13'); // 100（含税113元，税率13%，不含税100元）
$result = Math::taxExcluded('113', '0.13', 4); // 100.0000（保留4位小数）

// 简单利息计算
$result = Math::simpleInterest('10000', '0.05', 2); // 1000（本金10000元，年利率5%，2年利息）
$result = Math::simpleInterest('10000', '0.05', 2, 4); // 1000.0000（保留4位小数）

// 复利计算
$result = Math::compoundInterest('10000', '0.05', 2); // 11025（本金10000元，年利率5%，2年本息合计）
$result = Math::compoundInterest('10000', '0.05', 2, 4); // 11025.0000（保留4位小数）
```

### 统计分析

```php
use Kode\Math\Math;

// 平均值计算
$result = Math::average([1, 2, 3, 4, 5]); // 3
$result = Math::average([1, 2, 3, 4, 5], 4); // 3.0000（保留4位小数）

// 中位数计算
$result = Math::median([1, 2, 3, 4, 5]); // 3（奇数个元素取中间值）
$result = Math::median([1, 2, 3, 4]); // 2.5（偶数个元素取中间两个的平均值）
$result = Math::median([1, 2, 3, 4], 4); // 2.5000（保留4位小数）

// 众数计算
$result = Math::mode([1, 2, 2, 3, 3, 3, 4]); // 3（出现次数最多的值）

// 标准差计算
$result = Math::standardDeviation([1, 2, 3, 4, 5]); // 1.5811388301
$result = Math::standardDeviation([1, 2, 3, 4, 5], 4); // 1.5811（保留4位小数）

// 方差计算
$result = Math::variance([1, 2, 3, 4, 5]); // 2.5（标准差的平方）
$result = Math::variance([1, 2, 3, 4, 5], 4); // 2.5000（保留4位小数）
```

### 数值判断

```php
use Kode\Math\Math;

// 判断是否为正数
$result = Math::isPositive('5'); // true
$result = Math::isPositive('-5'); // false
$result = Math::isPositive('0'); // false

// 判断是否为负数
$result = Math::isNegative('-5'); // true
$result = Math::isNegative('5'); // false
$result = Math::isNegative('0'); // false

// 判断是否为零
$result = Math::isZero('0'); // true
$result = Math::isZero('0.0000000001', 10); // true（保留10位小数比较）
$result = Math::isZero('0.0000000001', 11); // false（保留11位小数比较）

// 判断是否为偶数
$result = Math::isEven(4); // true
$result = Math::isEven(5); // false

// 判断是否为奇数
$result = Math::isOdd(5); // true
$result = Math::isOdd(4); // false

// 判断是否为质数
$result = Math::isPrime(7); // true
$result = Math::isPrime(8); // false
$result = Math::isPrime(2); // true
$result = Math::isPrime(1); // false

// 判断数值是否有效
$result = Math::isValid('123'); // true
$result = Math::isValid('abc'); // false
$result = Math::isValid(INF); // false（无穷大）
$result = Math::isValid(NAN); // false（非数字）
```

### 数值范围和随机数

```php
use Kode\Math\Math;

// 数值范围检查
$result = Math::inRange('5', '1', '10'); // true（5在1到10之间）
$result = Math::inRange('0', '1', '10'); // false（0不在1到10之间）
$result = Math::inRange('11', '1', '10'); // false（11不在1到10之间）

// 限制数值范围
$result = Math::clamp('5', '1', '10'); // 5（5在范围内，保持不变）
$result = Math::clamp('0', '1', '10'); // 1（0小于最小值，返回最小值）
$result = Math::clamp('11', '1', '10'); // 10（11大于最大值，返回最大值）

// 生成随机数
$result = Math::random(1, 100); // 1到100之间的随机整数
$result = Math::random(1, 100, 2); // 1到100之间的随机浮点数，保留2位小数

// 线性插值
$result = Math::lerp(0, 100, 0.5); // 50（在0和100之间插值，因子为0.5）
$result = Math::lerp(0, 100, 0.25); // 25（在0和100之间插值，因子为0.25）
$result = Math::lerp(0, 100, 0.75); // 75（在0和100之间插值，因子为0.75）
```

## 地理位置模块使用示例

地理位置模块提供了坐标之间的距离计算、坐标验证、中点计算、方位角计算等功能，使用Haversine公式进行精确计算。

### 距离计算

```php
use Kode\Geo\Geo;

// 计算两点之间的距离（单位：公里）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737); // 1067.7（北京到上海的距离）

// 计算两点之间的距离（单位：英里）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'mi'); // 663.4（北京到上海的距离）

// 计算两点之间的距离（单位：米）
$distance = Geo::distance(39.9042, 116.4074, 31.2304, 121.4737, 'm'); // 1067700（北京到上海的距离）
```

### 坐标验证

```php
use Kode\Geo\Geo;

// 验证坐标是否有效
$isValid = Geo::isValid(39.9042, 116.4074); // true（北京坐标有效）
$isValid = Geo::isValid(91, 116.4074); // false（纬度超出范围[-90, 90]）
$isValid = Geo::isValid(39.9042, 181); // false（经度超出范围[-180, 180]）
```

### 中点计算

```php
use Kode\Geo\Geo;

// 计算两个坐标之间的中点
$midpoint = Geo::midpoint(39.9042, 116.4074, 31.2304, 121.4737);
// 输出: [35.5673, 118.9406]（北京和上海的中点坐标）
```

### 方位角计算

```php
use Kode\Geo\Geo;

// 计算两个坐标之间的方位角
$bearing = Geo::bearing(39.9042, 116.4074, 31.2304, 121.4737); // 137.5（从北京到上海的方位角）
```

### 坐标转换

```php
use Kode\Geo\Geo;

// 十进制度数转度分秒（DMS）
$dms = Geo::toDMS(39.9042, true); // [39, 54, 15.12, 'N']（39.9042° = 39°54'15.12"N）
$dms = Geo::toDMS(116.4074, false); // [116, 24, 26.64, 'E']（116.4074° = 116°24'26.64"E）

// 度分秒（DMS）转十进制度数
$decimal = Geo::toDecimal(39, 54, 15.12, 'N'); // 39.9042
$decimal = Geo::toDecimal(116, 24, 26.64, 'E'); // 116.4074
$decimal = Geo::toDecimal(39, 54, 15.12, 'S'); // -39.9042（南纬为负）
$decimal = Geo::toDecimal(116, 24, 26.64, 'W'); // -116.4074（西经为负）
```

### 角度转换

```php
use Kode\Geo\Geo;

// 角度转弧度
$radians = Geo::toRadians(90); // 1.5707963268（90度转弧度）

// 弧度转角度
$degrees = Geo::toDegrees(1.5707963268); // 90（1.5707963268弧度转角度）
```

## IP地址处理模块使用示例

IP地址处理模块提供了IP地址获取、验证、私有IP检测等功能，支持代理头获取真实IP。

### 获取客户端IP

```php
use Kode\Ip\Ip;

// 获取客户端IP
$ip = Ip::get(); // 192.168.1.1（客户端IP地址）

// 获取客户端IP（支持代理头）
$ip = Ip::get(true); // 8.8.8.8（从代理头获取真实IP）
```

### IP地址验证

```php
use Kode\Ip\Ip;

// 验证IP地址是否有效
$isValid = Ip::isValid('192.168.1.1'); // true
$isValid = Ip::isValid('8.8.8.8'); // true
$isValid = Ip::isValid('256.256.256.256'); // false（IP地址无效）
$isValid = Ip::isValid('abc'); // false（IP地址无效）
```

### 私有IP检测

```php
use Kode\Ip\Ip;

// 判断是否为私有IP
$isPrivate = Ip::isPrivate('192.168.1.1'); // true（私有IP）
$isPrivate = Ip::isPrivate('10.0.0.1'); // true（私有IP）
$isPrivate = Ip::isPrivate('172.16.0.1'); // true（私有IP）
$isPrivate = Ip::isPrivate('8.8.8.8'); // false（公网IP）

// 判断是否为公网IP
$isPublic = Ip::isPublic('8.8.8.8'); // true（公网IP）
$isPublic = Ip::isPublic('192.168.1.1'); // false（私有IP）
```

### IP地址类型判断

```php
use Kode\Ip\Ip;

// 判断IP地址类型
$type = Ip::getType('192.168.1.1'); // 'private'（私有IP）
$type = Ip::getType('8.8.8.8'); // 'public'（公网IP）
$type = Ip::getType('127.0.0.1'); // 'loopback'（回环地址）
$type = Ip::getType('256.256.256.256'); // 'invalid'（无效IP）
```

### IP地址格式化

```php
use Kode\Ip\Ip;

// 格式化IP地址
$formatted = Ip::format('192.168.1.1'); // '192.168.1.1'
$formatted = Ip::format('192.168.1.1', true); // '192.168.001.001'（补零格式化）
```

## 状态码列表

### 基础状态码

| 状态码 | 描述 |
|--------|------|
| 200 | success |
| 400 | bad request |
| 401 | unauthorized |
| 403 | forbidden |
| 404 | not found |
| 500 | internal server error |

### 业务状态码

| 状态码 | 描述 |
|--------|------|
| 300000 | token invalid |
| 300001 | token expired |
| 400000 | parameter error |
| 500000 | database error |
| 600000 | business logic error |

## 版本要求

- PHP >= 8.1
- ext-openssl
- ext-json

## 许可证

MIT License