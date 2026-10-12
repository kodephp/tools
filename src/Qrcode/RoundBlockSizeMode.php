<?php

declare(strict_types=1);

namespace Kode\Qrcode;

/**
 * QR码圆角样式枚举
 */
enum RoundBlockSizeMode: string
{
    #[\Deprecated('与上游 endroid/qr-code 的取值不对应（enlarge/margin/none/shrink）；请改用 Qr::roundStyle()', since: '2.1.0')]
    case MINUS = 'minus';
    #[\Deprecated('与上游 endroid/qr-code 的取值不对应（enlarge/margin/none/shrink）；请改用 Qr::roundStyle()', since: '2.1.0')]
    case NOMINAL = 'nominal';
    #[\Deprecated('与上游 endroid/qr-code 的取值不对应（enlarge/margin/none/shrink）；请改用 Qr::roundStyle()', since: '2.1.0')]
    case PLUS = 'plus';
    #[\Deprecated('与上游 endroid/qr-code 的取值不对应（enlarge/margin/none/shrink）；请改用 Qr::roundStyle()', since: '2.1.0')]
    case MURKY = 'murky';
}
