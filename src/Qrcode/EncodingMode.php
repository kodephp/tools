<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码编码模式枚举
 */
enum EncodingMode: string
{
    #[\Deprecated('QR 编码模式由上游 endroid/qr-code 内部自动选择，不可配置；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case AUTO = 'auto';
    #[\Deprecated('QR 编码模式由上游 endroid/qr-code 内部自动选择，不可配置；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case NUMERIC = 'numeric';
    #[\Deprecated('QR 编码模式由上游 endroid/qr-code 内部自动选择，不可配置；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case ALPHANUMERIC = 'alphanumeric';
    #[\Deprecated('QR 编码模式由上游 endroid/qr-code 内部自动选择，不可配置；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case BYTE = 'byte';
    #[\Deprecated('QR 编码模式由上游 endroid/qr-code 内部自动选择，不可配置；本枚举从未接入 Qr 构建', since: '2.1.0')]
    case KANJI = 'kanji';
}
