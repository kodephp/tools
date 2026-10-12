<?php

declare(strict_types=1);

namespace Kode\Qrcode;

use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\Writer\Result\AbstractResult;

/**
 * 突发缓存（burst cache）命中时的写入结果。
 *
 * 数据直接来自磁盘缓存；矩阵按需重建，供 ConsoleResult 等需要矩阵的消费方使用。
 */
final class CachedResult extends AbstractResult
{
    public function __construct(
        MatrixInterface $matrix,
        private readonly string $data,
        private readonly string $mimeType,
    ) {
        parent::__construct($matrix);
    }

    public function getString(): string
    {
        return $this->data;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
