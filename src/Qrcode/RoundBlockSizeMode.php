<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码圆角样式枚举
 */
enum RoundBlockSizeMode: string
{
    case MINUS = 'minus';
    case NOMINAL = 'nominal';
    case PLUS = 'plus';
    case MURKY = 'murky';
}
