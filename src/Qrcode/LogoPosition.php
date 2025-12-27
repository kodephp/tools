<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码Logo位置枚举
 */
enum LogoPosition: string
{
    case CENTER = 'center';
    case TOP = 'top';
    case BOTTOM = 'bottom';
}
