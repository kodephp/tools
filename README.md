# kode/tools - PHP8.3+ 通用工具包

## 简介

这是一个基于PHP8.3+特性开发的模块化通用工具包，提供了数组处理、字符串处理、时间处理、加解密、消息体等功能。支持对象和静态两种调用方式，兼容进程和协程环境。

## 核心特性

### 模块化架构
工具包采用清晰的模块化架构，每个模块专注于特定领域的功能：

```
src/
├── Array/          # 数组处理模块
│   └── Arr.php    # 数组操作类
├── String/        # 字符串处理模块
│   └── Str.php    # 字符串操作类
├── Time/          # 时间处理模块
│   └── Time.php   # 时间操作类
├── Crypto/        # 加解密模块
│   └── Crypto.php # 加解密操作类
├── Message/       # 消息体模块
│   ├── CodeMap.php # 状态码映射类
│   └── Message.php # 消息体操作类
└── Helper/        # 全局助手函数
    └── helper.php # 全局助手函数文件
```

### 数组处理模块
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
- ✅ 时间格式化
- ✅ 时间计算（加法、减法、差值）
- ✅ 常用时间获取（当前时间、今天、昨天、明天）
- ✅ 时区支持

### 加解密模块
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
- ✅ 高精度数学计算（加减乘除、取模、幂运算、平方根）
- ✅ 四舍五入、向上取整、向下取整
- ✅ 数值比较和格式化
- ✅ 解决浮点数精度丢失问题
- ✅ 支持PHP8.3+类型声明

### 消息体模块
- ✅ 双模式链式调用：实例链式 + 静态链式
- ✅ 完整的状态码管理（200/400/500基础码 + 6位业务码）
- ✅ 灵活的字段扩展和映射
- ✅ 内置分页支持
- ✅ 支持JSON直接输出
- ✅ PHP8.3+特性优化：只读属性、类型细化、nullsafe运算符
- ✅ 线程安全的初始化模式

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

## 数组深度合并使用示例

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
```

## 高精度数学计算使用示例

```php
use Kode\Math\Math;

// 高精度加法
$sum = Math::add(0.1, 0.2); // 0.3（解决浮点数精度丢失问题）

// 高精度减法
$diff = Math::sub(0.3, 0.1); // 0.2

// 高精度乘法
$product = Math::mul(0.1, 0.2); // 0.02

// 高精度除法
$quotient = Math::div(0.3, 0.1); // 3

// 四舍五入
$rounded = Math::round(0.12345, 2); // 0.12

// 数值比较
$equal = Math::equal(0.1 + 0.2, 0.3); // true

// 数值格式化
$formatted = Math::format(1234567.89, 2, true); // 1,234,567.89

// 三角函数
$sin = Math::sin(Math::deg2rad(30)); // 0.5
$cos = Math::cos(Math::deg2rad(60)); // 0.5
$tan = Math::tan(Math::deg2rad(45)); // 1.0

// 对数运算
$ln = Math::ln(exp(1)); // 1.0
$log10 = Math::log10(100); // 2.0
$log = Math::log(8, 2); // 3.0

// 角度弧度转换
$radians = Math::deg2rad(180); // 3.1415926536
$degrees = Math::rad2deg(M_PI); // 180.0
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
```

### 时间处理使用示例

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

### 加解密使用示例

```php
use Kode\Crypto\Crypto;

// 加密
$encrypted = Crypto::encrypt('敏感数据');

// 解密
$decrypted = Crypto::decrypt($encrypted);

// URL安全模式
$encrypted = Crypto::encrypt('敏感数据', mode: Crypto::MODE_URL_SAFE);

// 紧凑模式
$encrypted = Crypto::encrypt('敏感数据', mode: Crypto::MODE_COMPACT);
```

### 代码生成使用示例

#### 订单号生成

```php
use Kode\Crypto\Crypto;

// 生成订单号
$orderNo = Crypto::order('ORD');
// 输出示例: ORD202507011234567890
```

#### 邀请码生成

```php
// 生成6位邀请码
$inviteCode = Crypto::invite(6);
// 输出示例: ABC123

// 生成8位邀请码，仅使用字母
$inviteCode = Crypto::invite(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
// 输出示例: ABCDEFGH
```

#### URL安全码生成

```php
// 生成16位URL安全码
$urlSafeCode = Crypto::url(16);
// 输出示例: abcdefghijklmnop
```

#### 注册码生成

```php
// 生成16位注册码，4位分段
$registrationCode = Crypto::reg(16, 4, '-');
// 输出示例: ABCDEFGH-IJKLMNOP-QRSTUVWX-YZ123456
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
| `tree(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $childrenField = 'children')` | 数组转树形结构 | $list: 数组, $idField: ID字段名, $parentIdField: 父ID字段名, $childrenField: 子节点字段名 |
| `list(array $tree, string $childrenField = 'children')` | 树形结构转数组 | $tree: 树形结构, $childrenField: 子节点字段名 |
| `level(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $levelField = 'level')` | 数组转层级结构 | $list: 数组, $idField: ID字段名, $parentIdField: 父ID字段名, $levelField: 层级字段名 |
| `path(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $nameField = 'name', string $pathField = 'path', string $pathSeparator = '/')` | 数组转路径结构 | $list: 数组, $idField: ID字段名, $parentIdField: 父ID字段名, $nameField: 名称字段名, $pathField: 路径字段名, $pathSeparator: 路径分隔符 |

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