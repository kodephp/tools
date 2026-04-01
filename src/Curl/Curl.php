<?php

declare(strict_types=1);

namespace Kode\Curl;

use InvalidArgumentException;
use RuntimeException;
use Throwable;
use BadMethodCallException;

/**
 * HTTP请求工具类
 * 支持GET/POST/PUT/PATCH/DELETE等请求方式，提供链式调用
 */
class Curl
{
    /** GET请求 */
    public const METHOD_GET = 'GET';
    /** POST请求 */
    public const METHOD_POST = 'POST';
    /** PUT请求 */
    public const METHOD_PUT = 'PUT';
    /** PATCH请求 */
    public const METHOD_PATCH = 'PATCH';
    /** DELETE请求 */
    public const METHOD_DELETE = 'DELETE';
    /** HEAD请求 */
    public const METHOD_HEAD = 'HEAD';
    /** OPTIONS请求 */
    public const METHOD_OPTIONS = 'OPTIONS';

    /** JSON内容类型 */
    public const CONTENT_JSON = 'application/json';
    /** Form表单内容类型 */
    public const CONTENT_FORM = 'application/x-www-form-urlencoded';
    /** 多部分表单内容类型 */
    public const CONTENT_MULTI = 'multipart/form-data';
    /** XML内容类型 */
    public const CONTENT_XML = 'application/xml';
    /** 纯文本内容类型 */
    public const CONTENT_TEXT = 'text/plain';
    /** HTML内容类型 */
    public const CONTENT_HTML = 'text/html';

    /** PHP版本检测标志 */
    private static bool $php85Detected = false;
    /** PHP8.5+标志 */
    private static bool $isPhp85 = false;

    /** 全局默认配置 */
    protected static array $config = [
        'timeout' => 30,
        'connectTimeout' => 10,
        'verifySsl' => true,
        'followLocation' => true,
        'maxRedirects' => 5,
        'userAgent' => '',
        'autoReferer' => true,
        'decodeGzip' => true,
        'enableCookie' => false,
    ];

    /** 请求URL */
    private string $url = '';
    /** 请求方法 */
    private string $method = self::METHOD_GET;
    /** 请求头 */
    private array $headers = [];
    /** URL查询参数 */
    private array $queryParams = [];
    /** 请求体数据 */
    private mixed $body = null;
    /** CURL选项 */
    private array $options = [];
    /** 上传文件 */
    private array $files = [];
    /** 是否验证SSL */
    private bool $verifySsl = true;
    /** CA证书路径 */
    private ?string $caBundle = null;
    /** 超时时间（秒） */
    private int $timeout = 30;
    /** 连接超时时间（秒） */
    private int $connectTimeout = 10;
    /** 代理地址 */
    private ?string $proxy = null;
    /** 代理用户名 */
    private ?string $proxyUser = null;
    /** 代理密码 */
    private ?string $proxyPass = null;
    /** 是否跟随重定向 */
    private bool $followLocation = true;
    /** 最大重定向次数 */
    private int $maxRedirects = 5;
    /** 是否返回传输结果 */
    private bool $returnTransfer = true;
    /** 是否自动设置Referer */
    private bool $autoReferer = true;
    /** 是否解码Gzip */
    private bool $decodeGzip = true;
    /** 是否忽略错误 */
    private bool $ignoreErrors = false;
    /** User-Agent */
    private ?string $userAgent = null;
    /** Referer */
    private ?string $referer = null;
    /** 是否启用Cookie */
    private bool $enableCookie = false;
    /** Cookie文件 */
    private ?string $cookieFile = null;
    /** Cookie Jar文件 */
    private ?string $cookieJar = null;
    /** 重试次数 */
    private int $retryTimes = 0;
    /** 重试延迟（毫秒） */
    private int $retryDelay = 1000;
    /** 成功回调 */
    private array $successCallbacks = [];
    /** 错误回调 */
    private array $errorCallbacks = [];
    /** 中间件 */
    private array $middleware = [];

    /**
     * 构造函数
     * @param string $url 请求URL
     */
    public function __construct(string $url = '')
    {
        $this->url = $this->sanitizeUrl($url);
        $this->method = self::METHOD_GET;
        $this->userAgent = 'KodeCurl/1.0 PHP/' . PHP_VERSION;
    }

    /**
     * 初始化配置
     */
    protected static function initialize(): void
    {
        if (empty(static::$config)) {
            static::$config = [
                'timeout' => 30,
                'connectTimeout' => 10,
                'verifySsl' => true,
                'followLocation' => true,
                'maxRedirects' => 5,
                'userAgent' => '',
                'autoReferer' => true,
                'decodeGzip' => true,
                'enableCookie' => false,
            ];
        }
    }

    /**
     * 检测PHP版本
     * @return bool 是否为PHP8.5+
     */
    private static function detectPhp85(): bool
    {
        if (!self::$php85Detected) {
            self::$php85Detected = true;
            self::$isPhp85 = PHP_VERSION_ID >= 80500;
        }
        return self::$isPhp85;
    }

    /**
     * 验证并清理URL
     * @param string $url URL
     * @return string 清理后的URL
     */
    private function sanitizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL: {$url}");
        }
        return $url;
    }

    /**
     * 清理键名
     * @param string $key 键名
     * @return string 清理后的键名
     */
    private function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9-_]/', '', $key) ?: $key;
    }

    /**
     * 清理数据（XSS防护）
     * @param mixed $data 数据
     * @return mixed 清理后的数据
     */
    private function sanitizeData(mixed $data): mixed
    {
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        if (is_array($data)) {
            return array_map(fn($v) => $this->sanitizeData($v), $data);
        }
        return $data;
    }

    /**
     * 创建请求实例
     * @param string $url URL
     * @return static 实例
     */
    public static function create(string $url = ''): static
    {
        return new static($url);
    }

    /**
     * 发送GET请求
     * @param string $url URL
     * @param array $query 查询参数
     * @return static 实例
     */
    public static function get(string $url, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_GET)->query($query);
    }

    /**
     * 发送POST请求
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static 实例
     */
    public static function post(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_POST)->body($data)->query($query);
    }

    /**
     * 发送PUT请求
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static 实例
     */
    public static function put(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_PUT)->body($data)->query($query);
    }

    /**
     * 发送PATCH请求
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static 实例
     */
    public static function patch(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    /**
     * 发送DELETE请求
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static 实例
     */
    public static function delete(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    /**
     * 设置URL（链式调用）
     * @param string $url URL
     * @return static
     */
    public function url(string $url): static
    {
        $this->url = $this->sanitizeUrl($url);
        return $this;
    }

    /**
     * 设置请求方法（链式调用）
     * @param string $method 方法
     * @return static
     */
    public function method(string $method): static
    {
        $methods = [
            self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT,
            self::METHOD_PATCH, self::METHOD_DELETE, self::METHOD_HEAD, self::METHOD_OPTIONS
        ];
        $method = strtoupper($method);
        if (!in_array($method, $methods, true)) {
            throw new InvalidArgumentException("Invalid method: {$method}");
        }
        $this->method = $method;
        return $this;
    }

    /**
     * 使用GET方法请求指定URL（链式调用）
     * @param string $url URL
     * @param array $query 查询参数
     * @return static
     */
    public function asGet(string $url, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_GET)->query($query);
    }

    /**
     * 使用POST方法请求指定URL（链式调用）
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static
     */
    public function asPost(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_POST)->body($data)->query($query);
    }

    /**
     * 使用PUT方法请求指定URL（链式调用）
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static
     */
    public function asPut(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_PUT)->body($data)->query($query);
    }

    /**
     * 使用PATCH方法请求指定URL（链式调用）
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static
     */
    public function asPatch(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    /**
     * 使用DELETE方法请求指定URL（链式调用）
     * @param string $url URL
     * @param mixed $data 数据
     * @param array $query 查询参数
     * @return static
     */
    public function asDelete(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    /**
     * 批量设置请求头（链式调用）
     * @param array $headers 请求头
     * @return static
     */
    public function headers(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($this->sanitizeKey($key), (string)$value);
        }
        return $this;
    }

    /**
     * 设置单个请求头（链式调用）
     * @param string $key 头名称
     * @param string $value 头值
     * @return static
     */
    public function header(string $key, string $value): static
    {
        $this->headers[$this->sanitizeKey($key)] = $this->sanitizeData($value);
        return $this;
    }

    /**
     * 设置Accept头（链式调用）
     * @param string $contentType 内容类型
     * @return static
     */
    public function accept(string $contentType): static
    {
        return $this->header('Accept', $contentType);
    }

    /**
     * 设置Content-Type头（链式调用）
     * @param string $contentType 内容类型
     * @return static
     */
    public function contentType(string $contentType): static
    {
        return $this->header('Content-Type', $contentType);
    }

    /**
     * 设置Authorization头（链式调用）
     * @param string $token Token
     * @return static
     */
    public function authorization(string $token): static
    {
        return $this->header('Authorization', 'Bearer ' . $token);
    }

    /**
     * 设置Bearer Token（链式调用）
     * @param string $token Token
     * @return static
     */
    public function bearer(string $token): static
    {
        return $this->authorization($token);
    }

    /**
     * 设置Basic认证（链式调用）
     * @param string $username 用户名
     * @param string $password 密码
     * @return static
     */
    public function basicAuth(string $username, string $password): static
    {
        return $this->header('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
    }

    /**
     * 设置查询参数（链式调用）
     * @param array $params 参数
     * @return static
     */
    public function query(array $params): static
    {
        $this->queryParams = array_merge($this->queryParams, $this->sanitizeData($params));
        return $this;
    }

    /**
     * 设置单个查询参数（链式调用）
     * @param string $key 参数名
     * @param mixed $value 参数值
     * @return static
     */
    public function queryParam(string $key, mixed $value): static
    {
        $this->queryParams[$this->sanitizeKey($key)] = $this->sanitizeData($value);
        return $this;
    }

    /**
     * 设置请求体数据（链式调用）
     * @param mixed $data 数据
     * @return static
     */
    public function body(mixed $data): static
    {
        $this->body = $this->sanitizeData($data);
        return $this;
    }

    /**
     * 以JSON格式发送数据（链式调用）
     * @param mixed $data 数据
     * @return static
     */
    public function withJson(mixed $data = true): static
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_JSON)->header('Accept', self::CONTENT_JSON);
    }

    /**
     * 以表单格式发送数据（链式调用）
     * @param array $data 表单数据
     * @return static
     */
    public function withForm(array $data): static
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_FORM);
    }

    /**
     * 设置上传文件（链式调用）
     * @param array $files 文件信息
     * @return static
     */
    public function withFiles(array $files): static
    {
        $this->files = $files;
        return $this;
    }

    /**
     * 添加上传文件（链式调用）
     * @param string $field 字段名
     * @param string $path 文件路径
     * @param string|null $filename 自定义文件名
     * @return static
     */
    public function addFile(string $field, string $path, ?string $filename = null): static
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File not found: {$path}");
        }
        if (!is_readable($path)) {
            throw new InvalidArgumentException("File not readable: {$path}");
        }
        $this->files[$field] = [
            'path' => $path,
            'filename' => $filename ?? basename($path),
            'mime' => $this->detectMimeType($path),
        ];
        return $this;
    }

    /**
     * 设置超时时间（链式调用）
     * @param int $seconds 秒数
     * @return static
     */
    public function timeout(int $seconds): static
    {
        if ($seconds < 1 || $seconds > 300) {
            throw new InvalidArgumentException('Timeout must be between 1 and 300 seconds');
        }
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * 设置连接超时时间（链式调用）
     * @param int $seconds 秒数
     * @return static
     */
    public function connectTimeout(int $seconds): static
    {
        if ($seconds < 1 || $seconds > 60) {
            throw new InvalidArgumentException('Connect timeout must be between 1 and 60 seconds');
        }
        $this->connectTimeout = $seconds;
        return $this;
    }

    /**
     * 设置SSL验证（链式调用）
     * @param bool $verify 是否验证
     * @param string|null $caBundle CA证书路径
     * @return static
     */
    public function verifySsl(bool $verify = true, ?string $caBundle = null): static
    {
        $this->verifySsl = $verify;
        if ($caBundle !== null && !file_exists($caBundle)) {
            throw new InvalidArgumentException("CA bundle not found: {$caBundle}");
        }
        $this->caBundle = $caBundle;
        return $this;
    }

    /**
     * 设置SSL证书（链式调用）
     * @param string $certPath 证书路径
     * @param string|null $keyPath 密钥路径
     * @param string|null $password 密码
     * @return static
     */
    public function sslCert(string $certPath, ?string $keyPath = null, ?string $password = null): static
    {
        if (!file_exists($certPath)) {
            throw new InvalidArgumentException("SSL cert not found: {$certPath}");
        }
        $this->options[CURLOPT_SSLCERT] = $certPath;
        if ($keyPath !== null) {
            if (!file_exists($keyPath)) {
                throw new InvalidArgumentException("SSL key not found: {$keyPath}");
            }
            $this->options[CURLOPT_SSLKEY] = $keyPath;
        }
        if ($password !== null) {
            $this->options[CURLOPT_SSLCERTTYPE] = 'PEM';
            $this->options[CURLOPT_KEYPASSWD] = $password;
        }
        return $this;
    }

    /**
     * 设置代理（链式调用）
     * @param string|null $host 代理地址
     * @param int|null $port 代理端口
     * @param string|null $user 代理用户名
     * @param string|null $pass 代理密码
     * @return static
     */
    public function proxy(?string $host, ?int $port = null, ?string $user = null, ?string $pass = null): static
    {
        if ($host === null) {
            $this->proxy = null;
            $this->proxyUser = null;
            $this->proxyPass = null;
        } else {
            if (!filter_var($host, FILTER_VALIDATE_URL) && !filter_var($host, FILTER_VALIDATE_IP)) {
                throw new InvalidArgumentException("Invalid proxy host: {$host}");
            }
            $this->proxy = $host . ($port ? ":{$port}" : '');
            $this->proxyUser = $user;
            $this->proxyPass = $pass;
        }
        return $this;
    }

    /**
     * 设置是否跟随重定向（链式调用）
     * @param bool $follow 是否跟随
     * @return static
     */
    public function followLocation(bool $follow = true): static
    {
        $this->followLocation = $follow;
        return $this;
    }

    /**
     * 设置最大重定向次数（链式调用）
     * @param int $max 最大次数
     * @return static
     */
    public function maxRedirects(int $max): static
    {
        if ($max < 0 || $max > 20) {
            throw new InvalidArgumentException('Max redirects must be between 0 and 20');
        }
        $this->maxRedirects = $max;
        return $this;
    }

    /**
     * 设置是否自动设置Referer（链式调用）
     * @param bool $auto 是否自动
     * @return static
     */
    public function autoReferer(bool $auto = true): static
    {
        $this->autoReferer = $auto;
        return $this;
    }

    /**
     * 设置是否解码Gzip（链式调用）
     * @param bool $decode 是否解码
     * @return static
     */
    public function decodeGzip(bool $decode = true): static
    {
        $this->decodeGzip = $decode;
        return $this;
    }

    /**
     * 设置是否忽略错误（链式调用）
     * @param bool $ignore 是否忽略
     * @return static
     */
    public function ignoreErrors(bool $ignore = true): static
    {
        $this->ignoreErrors = $ignore;
        return $this;
    }

    /**
     * 设置User-Agent（链式调用）
     * @param string $agent User-Agent字符串
     * @return static
     */
    public function userAgent(string $agent): static
    {
        $this->userAgent = $this->sanitizeData($agent);
        return $this;
    }

    /**
     * 设置Referer（链式调用）
     * @param string $referer Referer URL
     * @return static
     */
    public function referer(string $referer): static
    {
        $this->referer = $this->sanitizeUrl($referer);
        return $this;
    }

    /**
     * 设置Cookie（链式调用）
     * @param bool $enable 是否启用
     * @param string|null $file Cookie文件路径
     * @return static
     */
    public function cookie(bool $enable = true, ?string $file = null): static
    {
        $this->enableCookie = $enable;
        $this->cookieFile = $file ?? sys_get_temp_dir() . '/kode_curl_cookie.txt';
        return $this;
    }

    /**
     * 设置Cookie Jar文件（链式调用）
     * @param string|null $path 文件路径
     * @return static
     */
    public function cookieJar(?string $path = null): static
    {
        $this->cookieJar = $path ?? sys_get_temp_dir() . '/kode_curl_cookie_jar.txt';
        return $this;
    }

    /**
     * 发送Cookie头（链式调用）
     * @param string $cookie Cookie字符串
     * @return static
     */
    public function sendCookie(string $cookie): static
    {
        return $this->header('Cookie', $cookie);
    }

    /**
     * 设置重试次数（链式调用）
     * @param int $times 重试次数
     * @param int $delayMs 重试延迟（毫秒）
     * @return static
     */
    public function retry(int $times, int $delayMs = 1000): static
    {
        if ($times < 0 || $times > 10) {
            throw new InvalidArgumentException('Retry times must be between 0 and 10');
        }
        $this->retryTimes = $times;
        $this->retryDelay = max(100, $delayMs);
        return $this;
    }

    /**
     * 设置成功回调（链式调用）
     * @param callable $callback 回调函数
     * @return static
     */
    public function onSuccess(callable $callback): static
    {
        $this->successCallbacks[] = $callback;
        return $this;
    }

    /**
     * 设置错误回调（链式调用）
     * @param callable $callback 回调函数
     * @return static
     */
    public function onError(callable $callback): static
    {
        $this->errorCallbacks[] = $callback;
        return $this;
    }

    /**
     * 添加中间件（链式调用）
     * @param callable $middleware 中间件函数
     * @return static
     */
    public function middleware(callable $middleware): static
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * 设置CURL选项（链式调用）
     * @param int $option 选项
     * @param mixed $value 值
     * @return static
     */
    public function option(int $option, mixed $value): static
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * 批量设置CURL选项（链式调用）
     * @param array $options 选项数组
     * @return static
     */
    public function curlOptions(array $options): static
    {
        $this->options = $this->options + $options;
        return $this;
    }

    /**
     * 构建完整URL
     * @return string 完整URL
     */
    public function buildUrl(): string
    {
        $url = $this->url;
        if (!empty($this->queryParams)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($this->queryParams);
        }
        return $url;
    }

    /**
     * 构建请求体
     * @return mixed 请求体数据
     */
    public function buildBody(): mixed
    {
        if (empty($this->files)) {
            return $this->body;
        }

        $boundary = '----KodeCurl' . bin2hex(random_bytes(16));
        $body = '';

        if ($this->body !== null && is_array($this->body)) {
            foreach ($this->body as $key => $value) {
                $body .= "--{$boundary}\r\n";
                $body .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
                $body .= $value . "\r\n";
            }
        }

        foreach ($this->files as $field => $file) {
            $body .= "--{$boundary}\r\n";
            $body .= 'Content-Disposition: form-data; name="' . $field . '"; filename="' . $file['filename'] . '"' . "\r\n";
            $body .= 'Content-Type: ' . $file['mime'] . "\r\n\r\n";
            $body .= file_get_contents($file['path']) . "\r\n";
        }

        $body .= "--{$boundary}--\r\n";

        $this->contentType(self::CONTENT_MULTI . "; boundary={$boundary}");

        return $body;
    }

    /**
     * 构建请求头数组
     * @return array 请求头数组
     */
    public function buildHeaders(): array
    {
        $headers = $this->headers;
        if ($this->body !== null && !isset($headers['Content-Type']) && empty($this->files)) {
            $type = is_array($this->body) ? self::CONTENT_FORM : self::CONTENT_TEXT;
            $headers['Content-Type'] = $type;
        }
        return array_map(fn($k, $v) => "{$k}: {$v}", array_keys($headers), $headers);
    }

    /**
     * 发送请求
     * @return Response 响应对象
     */
    public function send(): Response
    {
        $url = $this->buildUrl();
        $method = $this->method;
        $body = $this->buildBody();

        foreach ($this->middleware as $middleware) {
            $result = $middleware($this, $url, $method, $body);
            if ($result === false) {
                return new Response(null, 0, '', -1, 'Middleware cancelled request');
            }
            if (is_array($result)) {
                [$url, $method, $body] = $result;
            }
        }

        $ch = curl_init();

        try {
            $headers = $this->buildHeaders();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => $this->returnTransfer,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                CURLOPT_FOLLOWLOCATION => $this->followLocation,
                CURLOPT_MAXREDIRS => $this->maxRedirects,
                CURLOPT_AUTOREFERER => $this->autoReferer,
                CURLOPT_ENCODING => $this->decodeGzip ? 'gzip, deflate' : '',
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_REFERER => $this->referer ?? $url,
                CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
                CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
                CURLOPT_COOKIEFILE => $this->enableCookie ? $this->cookieFile : null,
                CURLOPT_COOKIEJAR => $this->cookieJar ?? ($this->enableCookie ? $this->cookieFile : null),
                CURLOPT_IGNORE_CONTENT_LENGTH => true,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);

            if ($this->caBundle !== null && file_exists($this->caBundle)) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->caBundle);
            }

            if ($body !== null && in_array($method, [self::METHOD_POST, self::METHOD_PUT, self::METHOD_PATCH, self::METHOD_DELETE], true)) {
                if (is_string($body)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
                }
            }

            if ($this->proxy !== null) {
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
                if ($this->proxyUser !== null) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser . ':' . $this->proxyPass);
                }
            }

            curl_setopt_array($ch, $this->options);

            if (self::detectPhp85()) {
                try {
                    $share = @\curl_share_init_persistent([]);
                    if ($share !== false) {
                        curl_setopt($ch, CURLSHOPT_SHARE, $share);
                    }
                } catch (Throwable) {
                }
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            curl_close($ch);

            if ($errno) {
                $responseObj = new Response(null, $httpCode, (string)($contentType ?: ''), $errno, $error);
                foreach ($this->errorCallbacks as $callback) {
                    $callback($responseObj, $this);
                }
                if ($this->retryTimes > 0 && $this->isRetryableError($errno)) {
                    return $this->performRetry($url, $method, $headers, $body);
                }
                return $responseObj;
            }

            $responseObj = new Response($response, $httpCode, $contentType ?? '', 0, '');
            foreach ($this->successCallbacks as $callback) {
                $callback($responseObj, $this);
            }

            return $responseObj;

        } catch (Throwable $e) {
            curl_close($ch);
            throw new RuntimeException('Curl request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 执行重试
     * @param string $url URL
     * @param string $method 方法
     * @param array $headers 请求头
     * @param mixed $body 请求体
     * @return Response 响应对象
     */
    private function performRetry(string $url, string $method, array $headers, mixed $body): Response
    {
        for ($i = 1; $i <= $this->retryTimes; $i++) {
            usleep($this->retryDelay * 1000);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                CURLOPT_FOLLOWLOCATION => $this->followLocation,
                CURLOPT_MAXREDIRS => $this->maxRedirects,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
                CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
                CURLOPT_POSTFIELDS => $body,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            curl_close($ch);

            if (!$errno && $httpCode < 500) {
                $responseObj = new Response($response, $httpCode, $contentType ?? '', 0, '');
                foreach ($this->successCallbacks as $callback) {
                    $callback($responseObj, $this);
                }
                return $responseObj;
            }
        }

        return new Response(null, 0, '', CURLE_OPERATION_TIMEDOUT, 'Retry attempts exhausted');
    }

    /**
     * 检查是否为可重试的错误
     * @param int $errno 错误码
     * @return bool 是否可重试
     */
    private function isRetryableError(int $errno): bool
    {
        return in_array($errno, [
            CURLE_COULDNT_RESOLVE_HOST,
            CURLE_COULDNT_CONNECT,
            CURLE_OPERATION_TIMEDOUT,
            CURLE_SSL_CONNECT_ERROR,
            CURLE_GOT_NOTHING,
        ]);
    }

    /**
     * 发送请求（send方法的别名）
     * @return Response 响应对象
     */
    public function request(): Response
    {
        return $this->send();
    }

    /**
     * 发送请求并返回数组
     * @return array 结果数组
     */
    public function toArray(): array
    {
        return $this->send()->toArray();
    }

    /**
     * 发送请求并返回JSON
     * @return string JSON字符串
     */
    public function toJson(): string
    {
        return $this->send()->toJson();
    }

    /**
     * 发送请求并返回解析后的JSON
     * @return array|null JSON数据
     */
    public function json(): ?array
    {
        return $this->send()->json();
    }

    /**
     * 发送请求并返回对象
     * @return object|null 对象
     */
    public function object(): ?object
    {
        return $this->send()->object();
    }

    /**
     * Promise风格的成功处理（链式调用）
     * @param callable $onFulfilled 成功回调
     * @param callable|null $onRejected 失败回调
     * @return static
     */
    public function then(callable $onFulfilled, ?callable $onRejected = null): static
    {
        $response = $this->send();

        if ($response->isSuccess()) {
            $result = $onFulfilled($response, $this);
            if ($result instanceof static) {
                return $result;
            }
        } elseif ($onRejected !== null) {
            $result = $onRejected($response, $this);
            if ($result instanceof static) {
                return $result;
            }
        }

        return $this;
    }

    /**
     * Promise风格的错误处理（链式调用）
     * @param callable $onRejected 错误回调
     * @return static
     */
    public function catch(callable $onRejected): static
    {
        $response = $this->send();

        if (!$response->isSuccess()) {
            $result = $onRejected($response, $this);
            if ($result instanceof static) {
                return $result;
            }
        }

        return $this;
    }

    /**
     * Promise风格的最终处理
     * @param callable $onFinally 最终回调
     */
    public function finally(callable $onFinally): void
    {
        try {
            $this->send();
        } finally {
            $onFinally($this);
        }
    }

    /**
     * 管道处理（链式调用）
     * @param self $next 下一个Curl实例
     * @return static
     */
    public function pipe(self $next): static
    {
        return $this->middleware(function ($curl, $url, $method, $body) use ($next) {
            $response = $curl->send();
            if ($response->isSuccess()) {
                return $next->send();
            }
            return $response;
        });
    }

    /**
     * 检测文件MIME类型
     * @param string $path 文件路径
     * @return string MIME类型
     */
    private function detectMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
        ];
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * 并发请求池
     * @param array $requests Curl实例数组
     * @return array 响应结果数组
     */
    public static function pool(array $requests): array
    {
        $multiHandle = curl_multi_init();
        $handles = [];
        $results = [];

        foreach ($requests as $key => $curl) {
            $ch = $curl->createHandle();
            curl_multi_add_handle($multiHandle, $ch);
            $handles[$key] = ['handle' => $ch, 'curl' => $curl];
        }

        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        foreach ($handles as $key => $item) {
            $ch = $item['handle'];
            $response = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            $results[$key] = new Response($response, $httpCode, $contentType ?? '', curl_errno($ch), curl_error($ch));
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $results;
    }

    /**
     * 静态方法调用
     * @param string $name 方法名
     * @param array $arguments 参数
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $methods = ['get', 'post', 'put', 'patch', 'delete', 'create', 'pool'];
        if (in_array($name, $methods, true)) {
            return (new static())->$name(...$arguments);
        }
        throw new BadMethodCallException("Static method {$name} does not exist");
    }

    /**
     * 实例方法调用
     * @param string $name 方法名
     * @param array $arguments 参数
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        $methods = [
            'url', 'method', 'headers', 'header', 'accept', 'contentType',
            'authorization', 'bearer', 'basicAuth', 'query', 'queryParam',
            'body', 'withJson', 'withForm', 'withFiles', 'addFile',
            'timeout', 'connectTimeout', 'verifySsl', 'sslCert', 'proxy',
            'followLocation', 'maxRedirects', 'autoReferer', 'decodeGzip',
            'ignoreErrors', 'userAgent', 'referer', 'cookie', 'cookieJar',
            'sendCookie', 'retry', 'onSuccess', 'onError', 'middleware',
            'option', 'curlOptions', 'request', 'toArray', 'toJson',
            'json', 'object', 'then', 'catch', 'finally', 'pipe'
        ];

        if (in_array($name, $methods, true)) {
            return $this->$name(...$arguments);
        }
        throw new BadMethodCallException("Method {$name} does not exist");
    }
}
