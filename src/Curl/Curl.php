<?php

declare(strict_types=1);

namespace Kode\Curl;

use InvalidArgumentException;
use RuntimeException;
use Throwable;
use BadMethodCallException;

class Curl
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_OPTIONS = 'OPTIONS';

    public const CONTENT_JSON = 'application/json';
    public const CONTENT_FORM = 'application/x-www-form-urlencoded';
    public const CONTENT_MULTI = 'multipart/form-data';
    public const CONTENT_XML = 'application/xml';
    public const CONTENT_TEXT = 'text/plain';
    public const CONTENT_HTML = 'text/html';

    private static bool $php85Detected = false;
    private static ?object $persistentShare = null;

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

    private string $url = '';
    private string $method = self::METHOD_GET;
    private array $headers = [];
    private array $queryParams = [];
    private mixed $body = null;
    private array $options = [];
    private array $files = [];
    private bool $verifySsl = true;
    private ?string $caBundle = null;
    private int $timeout = 30;
    private int $connectTimeout = 10;
    private ?string $proxy = null;
    private ?string $proxyUser = null;
    private ?string $proxyPass = null;
    private bool $followLocation = true;
    private int $maxRedirects = 5;
    private bool $returnTransfer = true;
    private bool $autoReferer = true;
    private bool $decodeGzip = true;
    private bool $ignoreErrors = false;
    private ?string $userAgent = null;
    private ?string $referer = null;
    private bool $enableCookie = false;
    private ?string $cookieFile = null;
    private ?string $cookieJar = null;
    private int $retryTimes = 0;
    private int $retryDelay = 1000;
    private array $successCallbacks = [];
    private array $errorCallbacks = [];
    private array $middleware = [];

    public function __construct(string $url = '')
    {
        $this->url = $this->sanitizeUrl($url);
        $this->method = self::METHOD_GET;
        $this->userAgent = 'KodeCurl/1.0 PHP/' . PHP_VERSION;
    }
    
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

    private static function detectPhp85(): bool
    {
        if (!self::$php85Detected) {
            self::$php85Detected = true;
            return PHP_VERSION_ID >= 80500;
        }
        return PHP_VERSION_ID >= 80500;
    }

    private function sanitizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL: {$url}");
        }
        return $url;
    }

    private function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9-_]/', '', $key) ?: $key;
    }

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

    public static function create(string $url = ''): static
    {
        return new static($url);
    }

    public static function get(string $url, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_GET)->query($query);
    }

    public static function post(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_POST)->body($data)->query($query);
    }

    public static function put(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_PUT)->body($data)->query($query);
    }

    public static function patch(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    public static function delete(string $url, mixed $data = null, array $query = []): static
    {
        return (new static($url))->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    public function url(string $url): static
    {
        $this->url = $this->sanitizeUrl($url);
        return $this;
    }

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

    public function asGet(string $url, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_GET)->query($query);
    }

    public function asPost(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_POST)->body($data)->query($query);
    }

    public function asPut(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_PUT)->body($data)->query($query);
    }

    public function asPatch(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    public function asDelete(string $url, mixed $data = null, array $query = []): static
    {
        return $this->url($url)->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    public function headers(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->header($this->sanitizeKey($key), (string)$value);
        }
        return $this;
    }

    public function header(string $key, string $value): static
    {
        $this->headers[$this->sanitizeKey($key)] = $this->sanitizeData($value);
        return $this;
    }

    public function accept(string $contentType): static
    {
        return $this->header('Accept', $contentType);
    }

    public function contentType(string $contentType): static
    {
        return $this->header('Content-Type', $contentType);
    }

    public function authorization(string $token): static
    {
        return $this->header('Authorization', 'Bearer ' . $token);
    }

    public function bearer(string $token): static
    {
        return $this->authorization($token);
    }

    public function basicAuth(string $username, string $password): static
    {
        return $this->header('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
    }

    public function query(array $params): static
    {
        $this->queryParams = array_merge($this->queryParams, $this->sanitizeData($params));
        return $this;
    }

    public function queryParam(string $key, mixed $value): static
    {
        $this->queryParams[$this->sanitizeKey($key)] = $this->sanitizeData($value);
        return $this;
    }

    public function body(mixed $data): static
    {
        $this->body = $this->sanitizeData($data);
        return $this;
    }

    public function withJson(mixed $data = true): static
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_JSON)->header('Accept', self::CONTENT_JSON);
    }

    public function withForm(array $data): static
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_FORM);
    }

    public function withFiles(array $files): static
    {
        $this->files = $files;
        return $this;
    }

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

    public function timeout(int $seconds): static
    {
        if ($seconds < 1 || $seconds > 300) {
            throw new InvalidArgumentException('Timeout must be between 1 and 300 seconds');
        }
        $this->timeout = $seconds;
        return $this;
    }

    public function connectTimeout(int $seconds): static
    {
        if ($seconds < 1 || $seconds > 60) {
            throw new InvalidArgumentException('Connect timeout must be between 1 and 60 seconds');
        }
        $this->connectTimeout = $seconds;
        return $this;
    }

    public function verifySsl(bool $verify = true, ?string $caBundle = null): static
    {
        $this->verifySsl = $verify;
        if ($caBundle !== null && !file_exists($caBundle)) {
            throw new InvalidArgumentException("CA bundle not found: {$caBundle}");
        }
        $this->caBundle = $caBundle;
        return $this;
    }

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

    public function followLocation(bool $follow = true): static
    {
        $this->followLocation = $follow;
        return $this;
    }

    public function maxRedirects(int $max): static
    {
        if ($max < 0 || $max > 20) {
            throw new InvalidArgumentException('Max redirects must be between 0 and 20');
        }
        $this->maxRedirects = $max;
        return $this;
    }

    public function autoReferer(bool $auto = true): static
    {
        $this->autoReferer = $auto;
        return $this;
    }

    public function decodeGzip(bool $decode = true): static
    {
        $this->decodeGzip = $decode;
        return $this;
    }

    public function ignoreErrors(bool $ignore = true): static
    {
        $this->ignoreErrors = $ignore;
        return $this;
    }

    public function userAgent(string $agent): static
    {
        $this->userAgent = $this->sanitizeData($agent);
        return $this;
    }

    public function referer(string $referer): static
    {
        $this->referer = $this->sanitizeUrl($referer);
        return $this;
    }

    public function cookie(bool $enable = true, ?string $file = null): static
    {
        $this->enableCookie = $enable;
        $this->cookieFile = $file ?? sys_get_temp_dir() . '/kode_curl_cookie.txt';
        return $this;
    }

    public function cookieJar(?string $path = null): static
    {
        $this->cookieJar = $path ?? sys_get_temp_dir() . '/kode_curl_cookie_jar.txt';
        return $this;
    }

    public function sendCookie(string $cookie): static
    {
        return $this->header('Cookie', $cookie);
    }

    public function retry(int $times, int $delayMs = 1000): static
    {
        if ($times < 0 || $times > 10) {
            throw new InvalidArgumentException('Retry times must be between 0 and 10');
        }
        $this->retryTimes = $times;
        $this->retryDelay = max(100, $delayMs);
        return $this;
    }

    public function onSuccess(callable $callback): static
    {
        $this->successCallbacks[] = $callback;
        return $this;
    }

    public function onError(callable $callback): static
    {
        $this->errorCallbacks[] = $callback;
        return $this;
    }

    public function middleware(callable $middleware): static
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function option(int $option, mixed $value): static
    {
        $this->options[$option] = $value;
        return $this;
    }

    public function curlOptions(array $options): static
    {
        $this->options = $this->options + $options;
        return $this;
    }

    public function buildUrl(): string
    {
        $url = $this->url;
        if (!empty($this->queryParams)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($this->queryParams);
        }
        return $url;
    }

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

    public function buildHeaders(): array
    {
        $headers = $this->headers;
        if ($this->body !== null && !isset($headers['Content-Type']) && empty($this->files)) {
            $type = is_array($this->body) ? self::CONTENT_FORM : self::CONTENT_TEXT;
            $headers['Content-Type'] = $type;
        }
        return array_map(fn($k, $v) => "{$k}: {$v}", array_keys($headers), $headers);
    }

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
                $responseObj = new Response(null, $httpCode, $contentType ?? '', $errno, $error);
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

    public function request(): Response
    {
        return $this->send();
    }

    public function toArray(): array
    {
        return $this->send()->toArray();
    }

    public function toJson(): string
    {
        return $this->send()->toJson();
    }

    public function json(): ?array
    {
        return $this->send()->json();
    }

    public function object(): ?object
    {
        return $this->send()->object();
    }

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

    public function finally(callable $onFinally): void
    {
        try {
            $this->send();
        } finally {
            $onFinally($this);
        }
    }

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

    private function createHandle(): mixed
    {
        $url = $this->buildUrl();
        $body = $this->buildBody();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_HTTPHEADER => $this->buildHeaders(),
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
        ]);

        if (self::detectPhp85()) {
            try {
                $share = @\curl_share_init_persistent([]);
                if ($share !== false) {
                    curl_setopt($ch, CURLSHOPT_SHARE, $share);
                }
            } catch (Throwable) {
            }
        }

        return $ch;
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $methods = ['get', 'post', 'put', 'patch', 'delete', 'create', 'pool'];
        if (in_array($name, $methods, true)) {
            return (new static())->$name(...$arguments);
        }
        throw new BadMethodCallException("Static method {$name} does not exist");
    }

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