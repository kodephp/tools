<?php

declare(strict_types=1);

namespace Kode\Tests;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\QrCode as QrCodeBuilder;
use GdImage;
use Kode\Qrcode\CachedResult;
use Kode\Qrcode\EncodingMode;
use Kode\Qrcode\LogoPosition;
use Kode\Qrcode\Qr;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Qrcode\Qr
 * @covers \Kode\Qrcode\CachedResult
 */
final class QrcodeTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/qrcode_test_' . uniqid('', true);
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        Qr::useBurstCache(false);
        $burst = sys_get_temp_dir() . '/qrcode_burst';
        if (is_dir($burst)) {
            foreach (glob($burst . '/*.png') ?: [] as $f) {
                @unlink($f);
            }
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tmpDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        @rmdir($this->tmpDir);
    }

    public function testDefaultPngGeneration(): void
    {
        $bytes = Qr::create('https://example.com')->toString();
        self::assertNotSame('', $bytes);
        self::assertSame("\x89PNG", substr($bytes, 0, 4));
    }

    public function testAllWritersProduceOutput(): void
    {
        $png = Qr::create('hello')->asPng()->toString();
        $svg = Qr::create('hello')->asSvg()->toString();
        self::assertSame("\x89PNG", substr($png, 0, 4));
        self::assertStringContainsString('<svg', $svg);

        if (function_exists('imagewebp')) {
            $webp = Qr::create('hello')->asWebP()->toString();
            self::assertNotSame('', $webp);
        }

        $eps = Qr::create('hello')->asEps()->toString();
        self::assertStringContainsString('%!PS', $eps);
    }

    public function testDataUriMimeTypes(): void
    {
        self::assertStringStartsWith('data:image/png;base64,', Qr::create('a')->asPng()->toDataUri());
        self::assertStringStartsWith('data:image/svg+xml;base64,', Qr::create('a')->asSvg()->toDataUri());
        self::assertStringStartsWith('data:application/eps;base64,', Qr::create('a')->asEps()->toDataUri());
        if (function_exists('imagewebp')) {
            self::assertStringStartsWith('data:image/webp;base64,', Qr::create('a')->asWebP()->toDataUri());
        }
    }

    public function testSizeAndMarginClamp(): void
    {
        $qr = Qr::create('clamp')->size(10)->margin(-5);
        $ref = new \ReflectionProperty(Qr::class, 'size');
        $ref->setAccessible(true);
        self::assertSame(50, $ref->getValue($qr));
        $ref = new \ReflectionProperty(Qr::class, 'margin');
        $ref->setAccessible(true);
        self::assertSame(0, $ref->getValue($qr));
        self::assertNotSame('', $qr->toString());
    }

    public function testColorChangesOutputAndClamps(): void
    {
        $default = Qr::create('color')->toString();
        $red = Qr::create('color')->foregroundColor(255, 0, 0)->toString();
        self::assertNotSame($default, $red);

        $color = Qr::color(300, -5, 10);
        self::assertSame(255, $color->getRed());
        self::assertSame(0, $color->getGreen());
        self::assertSame(10, $color->getBlue());
    }

    public function testLabelWithDefaultFont(): void
    {
        $bytes = Qr::create('label-default')->label('Kode')->toString();
        self::assertSame("\x89PNG", substr($bytes, 0, 4));
    }

    public function testLabelWithExplicitFont(): void
    {
        $font = dirname(__DIR__) . '/vendor/endroid/qr-code/assets/open_sans.ttf';
        self::assertFileExists($font);
        $bytes = Qr::create('label-custom')->label('Kode', 12, $font)->toString();
        self::assertSame("\x89PNG", substr($bytes, 0, 4));
    }

    public function testLogoOverlay(): void
    {
        $logo = $this->tmpDir . '/logo.png';
        $img = imagecreatetruecolor(24, 24);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagepng($img, $logo);
        imagedestroy($img);

        $bytes = Qr::create('logo')->logo($logo, 0.2)->toString();
        self::assertSame("\x89PNG", substr($bytes, 0, 4));
    }

    public function testRoundStyleModes(): void
    {
        foreach (['enlarge', 'margin', 'none', 'shrink', 'unknown'] as $mode) {
            $bytes = Qr::create('round')->roundStyle($mode)->toString();
            self::assertSame("\x89PNG", substr($bytes, 0, 4));
        }
        $ref = new \ReflectionProperty(Qr::class, 'roundBlockSizeMode');
        $ref->setAccessible(true);
        self::assertSame('margin', $ref->getValue(Qr::create('round')->roundStyle('unknown')));
    }

    public function testErrorCorrectionLevels(): void
    {
        for ($level = 1; $level <= 5; $level++) {
            $bytes = Qr::create('ec')->errorCorrectionLevel($level)->toString();
            self::assertSame("\x89PNG", substr($bytes, 0, 4));
        }
    }

    public function testMakeWithOptions(): void
    {
        $svg = Qr::make('make-svg', ['format' => 'svg', 'size' => 200, 'margin' => 4])->toString();
        self::assertStringContainsString('<svg', $svg);

        $png = Qr::make('make-png', [
            'size' => 120,
            'error_level' => 4,
            'fg_color' => [10, 20, 30],
            'bg_color' => [240, 240, 240],
        ])->toString();
        self::assertSame("\x89PNG", substr($png, 0, 4));

        if (function_exists('imagewebp')) {
            $webp = Qr::make('make-webp', ['format' => 'webp'])->toString();
            self::assertNotSame('', $webp);
        }
        $eps = Qr::make('make-eps', ['format' => 'eps'])->toString();
        self::assertStringContainsString('%!PS', $eps);

        $labeled = Qr::make('make-labeled', [
            'label' => 'Kode',
            'label_font_size' => 12,
            'encoding' => 'UTF-8',
        ])->toString();
        self::assertSame("\x89PNG", substr($labeled, 0, 4));

        $logo = $this->tmpDir . '/make-logo.png';
        $img = imagecreatetruecolor(16, 16);
        imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
        imagepng($img, $logo);
        imagedestroy($img);
        $withLogo = Qr::make('make-logo', ['logo' => $logo, 'logo_scale' => 0.2])->toString();
        self::assertSame("\x89PNG", substr($withLogo, 0, 4));
    }

    public function testStaticEncoders(): void
    {
        self::assertStringStartsWith('data:image/png;base64,', Qr::url('https://example.com'));

        $encoders = [
            Qr::wifi('ssid', 'secret'),
            Qr::vcard(['phone' => '13800138000'], 'San', 'Zhang'),
            Qr::email('a@b.com', '主题', '正文'),
            Qr::phone('13800138000'),
            Qr::sms('13800138000', '你好'),
            Qr::geo(31.23, 121.47, '上海'),
            Qr::bitcoin('addr', 0.5, 'label'),
            Qr::event('开会', '20261012T100000', '20261012T110000', '会议室', '周会'),
        ];
        foreach ($encoders as $encoder) {
            self::assertNotSame('', $encoder->toString());
        }
    }

    public function testOutputTypes(): void
    {
        $png = Qr::create('output')->output();
        if (function_exists('imagecreatefromstring')) {
            self::assertInstanceOf(GdImage::class, $png);
        }
        $svg = Qr::create('output')->asSvg()->output();
        self::assertIsString($svg);
        $eps = Qr::create('output')->asEps()->output();
        self::assertIsString($eps);
    }

    public function testSaveWritesFile(): void
    {
        $path = $this->tmpDir . '/saved.png';
        Qr::create('save')->save($path);
        self::assertFileExists($path);
        self::assertSame("\x89PNG", substr((string) file_get_contents($path), 0, 4));
    }

    public function testBurstCacheRoundTrip(): void
    {
        $burstDir = sys_get_temp_dir() . '/qrcode_burst';
        $before = glob($burstDir . '/*.png') ?: [];

        Qr::useBurstCache(true);
        $first = Qr::create('burst-content-1')->toString();
        $afterFirst = array_diff(glob($burstDir . '/*.png') ?: [], $before);
        self::assertCount(1, $afterFirst);

        $second = Qr::create('burst-content-1')->toString();
        self::assertSame($first, $second);
        self::assertCount(1, array_diff(glob($burstDir . '/*.png') ?: [], $before));

        Qr::create('burst-content-2')->toString();
        self::assertCount(2, array_diff(glob($burstDir . '/*.png') ?: [], $before));

        Qr::useBurstCache(false);
        Qr::create('burst-content-3')->toString();
        self::assertCount(2, array_diff(glob($burstDir . '/*.png') ?: [], $before));
    }

    public function testCachedResultUnit(): void
    {
        $matrix = (new MatrixFactory())->create(new QrCodeBuilder('cached'));
        $result = new CachedResult($matrix, 'payload', 'image/png');
        self::assertSame('payload', $result->getString());
        self::assertSame('image/png', $result->getMimeType());
        self::assertSame($matrix, $result->getMatrix());
        self::assertSame('data:image/png;base64,' . base64_encode('payload'), $result->getDataUri());

        $file = $this->tmpDir . '/cached.svg';
        $result2 = new CachedResult($matrix, '<svg/>', 'image/svg+xml');
        $result2->saveToFile($file);
        self::assertSame('<svg/>', file_get_contents($file));
    }

    public function testDeprecatedNoopMethodsEmitDeprecation(): void
    {
        $captured = [];
        set_error_handler(function (int $no, string $str) use (&$captured): bool {
            $captured[] = $str;
            return true;
        });
        try {
            $qr = Qr::create('deprecated');
            self::assertSame($qr, $qr->roundDots());
            self::assertSame($qr, $qr->circularDots(true, 12));
            self::assertSame($qr, $qr->gradient(0, 0, 0, 255, 255, 255));
        } finally {
            restore_error_handler();
        }
        self::assertCount(3, $captured);
        self::assertStringContainsString('roundStyle', $captured[0]);
        self::assertStringContainsString('roundStyle', $captured[1]);
        self::assertStringContainsString('foregroundColor', $captured[2]);
    }

    public function testDeadEnumsAreDeprecated(): void
    {
        $captured = [];
        set_error_handler(function (int $no, string $str) use (&$captured): bool {
            $captured[] = $str;
            return true;
        });
        try {
            $a = EncodingMode::AUTO;
            $b = LogoPosition::CENTER;
            $c = \Kode\Qrcode\RoundBlockSizeMode::MURKY;
        } finally {
            restore_error_handler();
        }
        self::assertInstanceOf(EncodingMode::class, $a);
        self::assertInstanceOf(LogoPosition::class, $b);
        self::assertInstanceOf(\Kode\Qrcode\RoundBlockSizeMode::class, $c);
        self::assertCount(3, $captured);
        self::assertStringContainsString('deprecated', $captured[0]);
    }

    public function testHelperFunctions(): void
    {
        $qr = qr_create('helper', 250, 8);
        self::assertInstanceOf(Qr::class, $qr);

        $qr = qr_text('helper-text', 250, null, '#ff0000', '#ffffff');
        self::assertInstanceOf(Qr::class, $qr);
        self::assertSame("\x89PNG", substr($qr->toString(), 0, 4));

        self::assertSame([255, 0, 128], qr_hex_to_rgb('#ff0080'));
        self::assertSame([17, 34, 51], qr_hex_to_rgb('#123'));
        self::assertSame([1, 2, 3], qr_hex_to_rgb('010203'));

        $color = qr_color(1, 2, 3);
        self::assertSame(1, $color->getRed());

        self::assertInstanceOf(Qr::class, qr_url('https://example.com'));
        self::assertInstanceOf(Qr::class, qr_wifi('ssid', 'pw'));
        self::assertInstanceOf(Qr::class, qr_email('a@b.com'));
        self::assertInstanceOf(Qr::class, qr_phone('13800138000'));
        self::assertInstanceOf(Qr::class, qr_sms('13800138000', 'hi'));
        self::assertInstanceOf(Qr::class, qr_vcard(['firstName' => 'San', 'lastName' => 'Zhang']));
        self::assertInstanceOf(Qr::class, qr_geo(31.2, 121.4));
    }
}
