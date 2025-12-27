<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码编码模式枚举
 */
enum EncodingMode: string
{
    case AUTO = 'auto';
    case NUMERIC = 'numeric';
    case ALPHANUMERIC = 'alphanumeric';
    case BYTE = 'byte';
    case KANJI = 'kanji';
}
