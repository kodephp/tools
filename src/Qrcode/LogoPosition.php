<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码Logo位置枚举
 */
enum LogoPosition: string
{
    #[\Deprecated('Logo 定位不受上游 endroid/qr-code 支持（Logo 恒居中）；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case CENTER = 'center';
    #[\Deprecated('Logo 定位不受上游 endroid/qr-code 支持（Logo 恒居中）；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case TOP = 'top';
    #[\Deprecated('Logo 定位不受上游 endroid/qr-code 支持（Logo 恒居中）；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case BOTTOM = 'bottom';
}
