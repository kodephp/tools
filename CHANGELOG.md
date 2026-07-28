# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/lang/zh-CN/).

## [1.8.3] - 2026-07-28

### Added

- 新增 `Kode\Security\Security` 安全模块，提供：
  - 滑动窗口限速（文件锁实现，FPM/Swoole/FrankenPHP/Workerman 通用）
  - CSRF Token 生成与验证（支持一次性 Token）
  - HMAC-SHA256 请求签名与验签（含时间戳防重放）
  - CIDR/IP 范围检查代理
  - 安全输入过滤（支持 GET/POST/COOKIE/REQUEST/SERVER）
  - XSS/SQL 基础清理
- 新增 `Ip::inCidr()` / `Ip::inRange()` 方法。
- 新增 `Str::mbStrcut()` / `Str::limitLength()` 方法。
- 新增 `Arr::pluck()` / `Arr::toTree()` / `Arr::fromTree()` 别名方法。
- 新增 `Crypto::cryptoMd5()` 等前缀别名，兼容旧命名规范。
- 新增全局辅助函数：`security_rate_limit`、`security_csrf_token`、`security_sign`、`security_input`、`security_xss_clean`、`str_mb_strcut`、`str_limit_length`、`arr_pluck`、`arr_to_tree`、`arr_from_tree`、`ip_in_cidr`、`ip_in_range` 等。
- 新增 PHPUnit 测试套件（51 个测试，108 个断言）。
- 新增 `phpstan.neon` 静态分析配置。
- 新增 `CHANGELOG.md`。

### Changed

- `Message` 模块彻底移除单例/静态共享实例，静态调用每次创建新实例，避免 Swoole / FrankenPHP / Workerman 等高并发长生命周期环境下的状态泄漏。
- `(new Message)` 与 `Message::` 两种调用方式完全等价。
- 统一在 PHP 文件顶部加入 `declare(strict_types=1)`。
- `Time.php` 修复 PHP 8.4 隐式 nullable 参数弃用警告。
- `Crypto::token()` 修复浮点长度导致的 `random_bytes()` 问题。
- `composer.json` 移除写死的 `version` 字段（版本由 Git tag 驱动），新增 `ext-bcmath` 依赖和 `phpunit/phpunit` 开发依赖。

### Fixed

- 修复自定义状态码映射未正确覆盖默认映射的问题。
- 修复 `Str::validateIdCard()` 正则中的 Unicode 转义导致 PCRE2 报错。
- 修复 `Base.php`、`Qr.php` 中的隐式 nullable 参数。

### Security

- `Message` 动态字段增加危险方法名黑名单，防止通过链式调用执行系统/文件/网络等敏感函数。
- `Message` 默认开启 sanitize，自动对 `msg` / `data` / 动态字段进行 XSS 过滤。
- `Security` 模块签名使用 `hash_equals` 恒时比较，防止时序攻击。
