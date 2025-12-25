<?php

namespace Kode\Message;

class CodeMap
{
    /**
     * 基础状态码映射（只读属性）
     */
    private readonly array $baseMap;
    
    /**
     * 用户自定义状态码文件路径
     */
    private ?string $customFile = null;

    public function __construct(?string $customFile = null)
    {
        $this->baseMap = [
        // HTTP成功状态码
        200 => 'success',
        201 => 'created',
        202 => 'accepted',
        204 => 'no content',
        
        // HTTP客户端错误状态码
        400 => 'bad request',
        401 => 'unauthorized',
        403 => 'forbidden',
        404 => 'not found',
        405 => 'method not allowed',
        406 => 'not acceptable',
        408 => 'request timeout',
        409 => 'conflict',
        
        // HTTP服务器错误状态码
        500 => 'internal server error',
        501 => 'not implemented',
        502 => 'bad gateway',
        503 => 'service unavailable',
        504 => 'gateway timeout',
        
        // 业务状态码
        300000 => 'token invalid',
        300001 => 'token expired',
        300002 => 'insufficient permissions',
        300003 => 'account locked',
        400000 => 'parameter error',
        400001 => 'missing required parameter',
        400002 => 'invalid parameter format',
        400003 => 'parameter out of range',
        500000 => 'database error',
        500002 => 'third party service error',
        500003 => 'cache error',
        600000 => 'business logic error',
        600001 => 'resource not found',
        600002 => 'resource already exists',
        600003 => 'operation failed',
        ];
        
        // 加载用户自定义状态码文件
        if ($customFile && file_exists($customFile)) {
            $this->customFile = $customFile;
            $this->loadCustomFile();
        }
    }
    
    /**
     * 加载用户自定义状态码文件
     * @return void
     */
    public function loadCustomFile(): void
    {
        if ($this->customFile && file_exists($this->customFile)) {
            $customMap = require $this->customFile;
            if (is_array($customMap)) {
                $this->customMap = array_merge($this->customMap, $customMap);
            }
        }
    }
    
    /**
     * 重新加载用户自定义状态码文件
     * @return void
     */
    public function reloadCustomFile(): void
    {
        $this->customMap = [];
        $this->loadCustomFile();
    }
    
    /**
     * 自定义状态码映射
     */
    private array $customMap = [];
    
    /**
     * 设置自定义状态码映射
     * @param array $map 自定义映射数组
     * @return void
     */
    public function setMap(array $map): void
    {
        $this->customMap = array_merge($this->customMap, $map);
    }
    
    /**
     * 添加单个状态码
     * @param int $code 状态码
     * @param string $msg 状态消息
     * @return void
     */
    public function addCode(int $code, string $msg): void
    {
        $this->customMap[$code] = $msg;
    }
    
    /**
     * 移除自定义状态码
     * @param int $code 状态码
     * @return void
     */
    public function removeCode(int $code): void
    {
        if (isset($this->customMap[$code])) {
            unset($this->customMap[$code]);
        }
    }
    
    /**
     * 清除所有自定义状态码
     * @return void
     */
    public function clearCustomCodes(): void
    {
        $this->customMap = [];
    }
    
    /**
     * 合并状态码映射
     * @param array $map 要合并的映射数组
     * @return void
     */
    public function mergeCodes(array $map): void
    {
        $this->customMap = array_merge($this->customMap, $map);
    }
    
    /**
     * 获取状态码对应消息
     * @param int $code 状态码
     * @return string|null 状态消息
     */
    public function getMsg(int $code): ?string
    {
        // 优先返回自定义映射
        if (isset($this->customMap[$code])) {
            return $this->customMap[$code];
        }
        
        // 其次返回基础映射
        if (isset($this->baseMap[$code])) {
            return $this->baseMap[$code];
        }
        
        // 最后返回null
        return null;
    }
    
    /**
     * 检查状态码是否存在
     * @param int $code 状态码
     * @return bool 是否存在
     */
    public function hasCode(int $code): bool
    {
        return isset($this->baseMap[$code]) || isset($this->customMap[$code]);
    }
    
    /**
     * 获取所有状态码映射
     * @return array 所有状态码映射
     */
    public function getAllCodes(): array
    {
        return array_merge($this->baseMap, $this->customMap);
    }
    
    /**
     * 获取基础状态码映射
     * @return array 基础状态码映射
     */
    public function getBaseCodes(): array
    {
        return $this->baseMap;
    }
    
    /**
     * 获取自定义状态码映射
     * @return array 自定义状态码映射
     */
    public function getCustomCodes(): array
    {
        return $this->customMap;
    }
    
    /**
     * 判断是否是成功状态码
     * @param int $code 状态码
     * @return bool 是否是成功状态码
     */
    public function isSuccess(int $code): bool
    {
        return $code >= 200 && $code < 300;
    }
    
    /**
     * 判断是否是错误状态码
     * @param int $code 状态码
     * @return bool 是否是错误状态码
     */
    public function isError(int $code): bool
    {
        return $code >= 400;
    }
    
    /**
     * 判断是否是客户端错误
     * @param int $code 状态码
     * @return bool 是否是客户端错误
     */
    public function isClientError(int $code): bool
    {
        return $code >= 400 && $code < 500;
    }
    
    /**
     * 判断是否是服务器错误
     * @param int $code 状态码
     * @return bool 是否是服务器错误
     */
    public function isServerError(int $code): bool
    {
        return $code >= 500 && $code < 600;
    }
    
    /**
     * 判断是否是业务错误
     * @param int $code 状态码
     * @return bool 是否是业务错误
     */
    public function isBusinessError(int $code): bool
    {
        return $code >= 600000 && $code < 700000;
    }
    
    /**
     * 判断是否是认证错误
     * @param int $code 状态码
     * @return bool 是否是认证错误
     */
    public function isAuthError(int $code): bool
    {
        return in_array($code, [401, 403, 300000, 300001, 300002]);
    }
}