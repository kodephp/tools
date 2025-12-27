<?php

declare(strict_types=1);

namespace Kode\Curl;

use InvalidArgumentException;
use RuntimeException;

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

    public const ENGINE_CURL = 'curl';
    public const ENGINE_PERSISTENT = 'persistent';

    private static ?object $persistentShare = null;
    private static bool $php85Detected = false;

    private static bool $curlShareSupported = false;
    private static bool $curlSharePersistentSupported = false;

    private string $url;
    private string $method;
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

    private static function detectPhp85(): bool
    {
        if (self::$php85Detected) {
            return PHP_VERSION_ID >= 80500;
        }
        self::$php85Detected = true;
        return PHP_VERSION_ID >= 80500;
    }

    public static function getEngine(): string
    {
        return self::ENGINE_CURL;
    }

    public static function getPersistentEngine(): string
    {
        return self::detectPhp85() ? self::ENGINE_PERSISTENT : self::ENGINE_CURL;
    }

    private static function initPersistentShare(): object
    {
        if (self::$persistentShare !== null) {
            return self::$persistentShare;
        }

        if (self::detectPhp85()) {
            try {
                $shareOpts = @\curl_share_init_persistent([]);
                self::$persistentShare = $shareOpts ?: (object) ['type' => 'fallback'];
            } catch (\Throwable) {
                self::$persistentShare = (object) ['type' => 'fallback'];
            }
        } else {
            self::$persistentShare = (object) ['type' => 'fallback'];
        }

        return self::$persistentShare;
    }

    public function __construct(string $url = '')
    {
        $this->url = $url;
        $this->method = self::METHOD_GET;
        $this->userAgent = 'KodeCurl/1.0 PHP/' . PHP_VERSION;
    }

    public static function create(string $url = ''): self
    {
        return new self($url);
    }

    public static function get(string $url, array $query = []): self
    {
        return (new self($url))->method(self::METHOD_GET)->query($query);
    }

    public static function post(string $url, mixed $data = null, array $query = []): self
    {
        return (new self($url))->method(self::METHOD_POST)->body($data)->query($query);
    }

    public static function put(string $url, mixed $data = null, array $query = []): self
    {
        return (new self($url))->method(self::METHOD_PUT)->body($data)->query($query);
    }

    public static function patch(string $url, mixed $data = null, array $query = []): self
    {
        return (new self($url))->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    public static function delete(string $url, mixed $data = null, array $query = []): self
    {
        return (new self($url))->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function method(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function asGet(string $url, array $query = []): self
    {
        return $this->url($url)->method(self::METHOD_GET)->query($query);
    }

    public function asPost(string $url, mixed $data = null, array $query = []): self
    {
        return $this->url($url)->method(self::METHOD_POST)->body($data)->query($query);
    }

    public function asPut(string $url, mixed $data = null, array $query = []): self
    {
        return $this->url($url)->method(self::METHOD_PUT)->body($data)->query($query);
    }

    public function asPatch(string $url, mixed $data = null, array $query = []): self
    {
        return $this->url($url)->method(self::METHOD_PATCH)->body($data)->query($query);
    }

    public function asDelete(string $url, mixed $data = null, array $query = []): self
    {
        return $this->url($url)->method(self::METHOD_DELETE)->body($data)->query($query);
    }

    public function asJson(mixed $data = true): self
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_JSON)->header('Accept', self::CONTENT_JSON);
    }

    public function headers(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function accept(string $contentType): self
    {
        return $this->header('Accept', $contentType);
    }

    public function contentType(string $contentType): self
    {
        return $this->header('Content-Type', $contentType);
    }

    public function authorization(string $token): self
    {
        return $this->header('Authorization', 'Bearer ' . $token);
    }

    public function bearer(string $token): self
    {
        return $this->authorization($token);
    }

    public function basicAuth(string $username, string $password): self
    {
        return $this->header('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
    }

    public function query(array $params): self
    {
        $this->queryParams = array_merge($this->queryParams, $params);
        return $this;
    }

    public function queryParam(string $key, mixed $value): self
    {
        $this->queryParams[$key] = $value;
        return $this;
    }

    public function body(mixed $data): self
    {
        $this->body = $data;
        return $this;
    }

    public function withJson(mixed $data = true): self
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_JSON)->header('Accept', self::CONTENT_JSON);
    }

    public function withForm(array $data): self
    {
        $this->body = $data;
        return $this->contentType(self::CONTENT_FORM);
    }

    public function withFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }

    public function addFile(string $field, string $path, ?string $filename = null): self
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File not found: {$path}");
        }
        $this->files[$field] = [
            'path' => $path,
            'filename' => $filename ?? basename($path),
            'mime' => $this->detectMimeType($path),
        ];
        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function connectTimeout(int $seconds): self
    {
        $this->connectTimeout = $seconds;
        return $this;
    }

    public function verifySsl(bool $verify = true, ?string $caBundle = null): self
    {
        $this->verifySsl = $verify;
        $this->caBundle = $caBundle;
        return $this;
    }

    public function sslCert(string $certPath, ?string $keyPath = null, ?string $password = null): self
    {
        $this->options[CURLOPT_SSLCERT] = $certPath;
        if ($keyPath !== null) {
            $this->options[CURLOPT_SSLKEY] = $keyPath;
        }
        if ($password !== null) {
            $this->options[CURLOPT_SSLCERTTYPE] = 'PEM';
            $this->options[CURLOPT_KEYPASSWD] = $password;
        }
        return $this;
    }

    public function proxy(?string $host, ?int $port = null, ?string $user = null, ?string $pass = null): self
    {
        if ($host === null) {
            $this->proxy = null;
            $this->proxyUser = null;
            $this->proxyPass = null;
        } else {
            $this->proxy = $host . ($port ? ":{$port}" : '');
            $this->proxyUser = $user;
            $this->proxyPass = $pass;
        }
        return $this;
    }

    public function followLocation(bool $follow = true): self
    {
        $this->followLocation = $follow;
        return $this;
    }

    public function maxRedirects(int $max): self
    {
        $this->maxRedirects = $max;
        return $this;
    }

    public function autoReferer(bool $auto = true): self
    {
        $this->autoReferer = $auto;
        return $this;
    }

    public function decodeGzip(bool $decode = true): self
    {
        $this->decodeGzip = $decode;
        return $this;
    }

    public function ignoreErrors(bool $ignore = true): self
    {
        $this->ignoreErrors = $ignore;
        return $this;
    }

    public function userAgent(string $agent): self
    {
        $this->userAgent = $agent;
        return $this;
    }

    public function referer(string $referer): self
    {
        $this->referer = $referer;
        return $this;
    }

    public function cookie(bool $enable = true, ?string $file = null): self
    {
        $this->enableCookie = $enable;
        $this->cookieFile = $file ?? sys_get_temp_dir() . '/kode_curl_cookie.txt';
        return $this;
    }

    public function cookieJar(?string $path = null): self
    {
        $this->cookieJar = $path ?? sys_get_temp_dir() . '/kode_curl_cookie_jar.txt';
        return $this;
    }

    public function sendCookie(string $cookie): self
    {
        return $this->header('Cookie', $cookie);
    }

    public function retry(int $times, int $delayMs = 1000): self
    {
        $this->retryTimes = $times;
        $this->retryDelay = $delayMs;
        return $this;
    }

    public function onSuccess(callable $callback): self
    {
        $this->successCallbacks[] = $callback;
        return $this;
    }

    public function onError(callable $callback): self
    {
        $this->errorCallbacks[] = $callback;
        return $this;
    }

    public function option(int $option, mixed $value): self
    {
        $this->options[$option] = $value;
        return $this;
    }

    public function curlOptions(array $options): self
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
                curl_setopt($ch, CURLSHOPT_SHARE, self::initPersistentShare());
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

        } catch (\Throwable $e) {
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
                CURLOPT_RETURNTRANSFER => $this->returnTransfer,
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
            curl_setopt($ch, CURLSHOPT_SHARE, self::initPersistentShare());
        }

        return $ch;
    }
}
