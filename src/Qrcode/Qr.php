<?php

declare(strict_types=1);

namespace Kode\Qrcode;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode as QrCodeBuilder;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WebPWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\WriterInterface;

class Qr
{
    private static bool $useBurstCache = false;

    private string $text = '';

    private int $size = 300;

    private int $margin = 10;

    private string $errorCorrectionLevel = 'medium';

    private ?ColorInterface $foregroundColor = null;

    private ?ColorInterface $backgroundColor = null;

    private ?Logo $logo = null;

    private ?Label $label = null;

    private string $roundBlockSizeMode = 'margin';

    private string $encoding = 'UTF-8';

    private string $writerClass = PngWriter::class;

    private bool $roundDots = false;

    private bool $useCircularDots = false;

    private int $dotSizeScale = 11;

    private ?ColorInterface $gradientStartColor = null;

    private ?ColorInterface $gradientEndColor = null;

    private string $gradientDirection = 'horizontal';

    public function __construct(string $text = '')
    {
        $this->text = $text;
        $this->foregroundColor = new Color(0, 0, 0);
        $this->backgroundColor = new Color(255, 255, 255);
    }

    public static function create(string $text = ''): self
    {
        return new self($text);
    }

    public function text(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function size(int $size): self
    {
        $this->size = max(50, $size);
        return $this;
    }

    public function margin(int $margin): self
    {
        $this->margin = max(0, $margin);
        return $this;
    }

    public function errorCorrectionLevel(int $level): self
    {
        $this->errorCorrectionLevel = (string) match ($level) {
            1 => 'low',
            2, 3 => 'medium',
            4 => 'quartile',
            5 => 'high',
            default => 'medium',
        };
        return $this;
    }

    public function foregroundColor(int $red, int $green, int $blue): self
    {
        $this->foregroundColor = self::color($red, $green, $blue);
        return $this;
    }

    public function backgroundColor(int $red, int $green, int $blue): self
    {
        $this->backgroundColor = self::color($red, $green, $blue);
        return $this;
    }

    public function logo(string $path, float $scale = 0.3): self
    {
        $this->logo = new Logo($path, max(50, (int) ($this->size * $scale)));
        return $this;
    }

    public function label(string $text, int $fontSize = 16, ?string $font = null): self
    {
        $textColor = $this->foregroundColor ?? new Color(0, 0, 0);
        $this->label = $font === null
            ? new Label(
                $text,
                alignment: LabelAlignment::Center,
                margin: new Margin(0, 10, 10, 10),
                textColor: $textColor
            )
            : new Label(
                $text,
                new Font($font, $fontSize),
                LabelAlignment::Center,
                new Margin(0, 10, 10, 10),
                $textColor
            );
        return $this;
    }

    public function roundStyle(string $mode): self
    {
        $this->roundBlockSizeMode = match ($mode) {
            'enlarge' => 'enlarge',
            'margin' => 'margin',
            'none' => 'none',
            'shrink' => 'shrink',
            default => 'margin',
        };
        return $this;
    }

    #[\Deprecated('上游 endroid/qr-code 无圆点渲染能力，本方法为无操作；请改用 roundStyle("enlarge") 获得圆角模块效果', since: '2.1.0')]
    public function roundDots(bool $use = true): self
    {
        $this->roundDots = $use;
        return $this;
    }

    #[\Deprecated('上游 endroid/qr-code 无圆点尺寸缩放能力，本方法为无操作；请改用 roundStyle("enlarge")', since: '2.1.0')]
    public function circularDots(bool $use = true, int $sizeScale = 11): self
    {
        $this->useCircularDots = $use;
        $this->dotSizeScale = max(8, min(16, $sizeScale));
        return $this;
    }

    #[\Deprecated('上游 endroid/qr-code 不支持渐变色，本方法为无操作；请改用 foregroundColor()/backgroundColor()', since: '2.1.0')]
    public function gradient(int $startRed, int $startGreen, int $startBlue, int $endRed, int $endGreen, int $endBlue, string $direction = 'horizontal'): self
    {
        $this->gradientStartColor = new Color($startRed, $startGreen, $startBlue);
        $this->gradientEndColor = new Color($endRed, $endGreen, $endBlue);
        $this->gradientDirection = match ($direction) {
            'horizontal', 'left_to_right' => 'horizontal',
            'vertical', 'top_to_bottom' => 'vertical',
            'diagonal', 'top_left_to_bottom_right' => 'diagonal',
            'diagonal_inverse', 'bottom_left_to_top_right' => 'diagonal_inverse',
            default => 'horizontal',
        };
        return $this;
    }

    public function encoding(string $encoding): self
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function asPng(): self
    {
        $this->writerClass = PngWriter::class;
        return $this;
    }

    public function asSvg(): self
    {
        $this->writerClass = SvgWriter::class;
        return $this;
    }

    public function asWebP(): self
    {
        $this->writerClass = WebPWriter::class;
        return $this;
    }

    public function asEps(): self
    {
        $this->writerClass = EpsWriter::class;
        return $this;
    }

    public static function useBurstCache(bool $use = true): void
    {
        self::$useBurstCache = $use;
    }

    private function createQrCode(): QrCodeBuilder
    {
        $errorCorrectionLevel = match ($this->errorCorrectionLevel) {
            'low' => ErrorCorrectionLevel::Low,
            'medium' => ErrorCorrectionLevel::Medium,
            'quartile' => ErrorCorrectionLevel::Quartile,
            'high' => ErrorCorrectionLevel::High,
            default => ErrorCorrectionLevel::Medium,
        };

        $roundBlockSizeMode = match ($this->roundBlockSizeMode) {
            'enlarge' => RoundBlockSizeMode::Enlarge,
            'margin' => RoundBlockSizeMode::Margin,
            'none' => RoundBlockSizeMode::None,
            'shrink' => RoundBlockSizeMode::Shrink,
            default => RoundBlockSizeMode::Margin,
        };

        return new QrCodeBuilder(
            $this->text,
            new Encoding($this->encoding),
            $errorCorrectionLevel,
            $this->size,
            $this->margin,
            $roundBlockSizeMode,
            $this->foregroundColor ?? new Color(0, 0, 0),
            $this->backgroundColor ?? new Color(255, 255, 255)
        );
    }

    public function build(): WriterInterface
    {
        $writerClass = $this->writerClass;
        return new $writerClass();
    }

    private function mimeType(): string
    {
        return match ($this->writerClass) {
            SvgWriter::class => 'image/svg+xml',
            WebPWriter::class => 'image/webp',
            EpsWriter::class => 'application/eps',
            default => 'image/png',
        };
    }

    private function extension(): string
    {
        return match ($this->writerClass) {
            SvgWriter::class => 'svg',
            WebPWriter::class => 'webp',
            EpsWriter::class => 'eps',
            default => 'png',
        };
    }

    /**
     * 突发缓存文件路径：以全部渲染输入的内容哈希命名，内容寻址、不可变。
     * 序列化失败（如上游值对象未来不可序列化）时返回 null，跳过缓存。
     */
    private function burstCacheFile(): ?string
    {
        try {
            $payload = serialize([
                $this->text,
                $this->encoding,
                $this->errorCorrectionLevel,
                $this->size,
                $this->margin,
                $this->roundBlockSizeMode,
                $this->foregroundColor,
                $this->backgroundColor,
                $this->logo,
                $this->label,
                $this->writerClass,
            ]);
        } catch (\Throwable) {
            return null;
        }
        return sys_get_temp_dir() . '/qrcode_burst/' . sha1($payload) . '.' . $this->extension();
    }

    private function writeResult(): ResultInterface
    {
        $qrCode = $this->createQrCode();
        $cacheFile = self::$useBurstCache ? $this->burstCacheFile() : null;

        if ($cacheFile !== null && is_file($cacheFile)) {
            $cached = file_get_contents($cacheFile);
            if ($cached !== false) {
                return new CachedResult(
                    (new MatrixFactory())->create($qrCode),
                    $cached,
                    $this->mimeType()
                );
            }
        }

        $result = $this->build()->write($qrCode, $this->logo, $this->label);

        if ($cacheFile !== null) {
            $dir = dirname($cacheFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($cacheFile, $result->getString(), LOCK_EX);
        }

        return $result;
    }

    public function toDataUri(): string
    {
        $result = $this->writeResult();
        return 'data:' . $this->mimeType() . ';base64,' . base64_encode($result->getString());
    }

    public function toString(): string
    {
        $result = $this->writeResult();
        return $result->getString();
    }

    public function save(string $path): void
    {
        $result = $this->writeResult();
        $result->saveToFile($path);
    }

    public function output(): \GdImage|false|string
    {
        $result = $this->writeResult();

        return match ($this->writerClass) {
            PngWriter::class, WebPWriter::class => imagecreatefromstring($result->getString()),
            SvgWriter::class => $result->getString(),
            default => $result->getString(),
        };
    }

    public static function make(string $text, array $options = []): self
    {
        $instance = new self($text);

        if (isset($options['size'])) {
            $instance->size((int) $options['size']);
        }
        if (isset($options['margin'])) {
            $instance->margin((int) $options['margin']);
        }
        if (isset($options['error_level'])) {
            $instance->errorCorrectionLevel((int) $options['error_level']);
        }
        if (isset($options['fg_color']) && is_array($options['fg_color'])) {
            $instance->foregroundColor(
                (int) $options['fg_color'][0],
                (int) $options['fg_color'][1],
                (int) $options['fg_color'][2]
            );
        }
        if (isset($options['bg_color']) && is_array($options['bg_color'])) {
            $instance->backgroundColor(
                (int) $options['bg_color'][0],
                (int) $options['bg_color'][1],
                (int) $options['bg_color'][2]
            );
        }
        if (isset($options['round_dots'])) {
            $instance->roundDots((bool) $options['round_dots']);
        }
        if (isset($options['circular_dots'])) {
            $instance->circularDots((bool) $options['circular_dots']);
        }
        if (isset($options['round_style'])) {
            $instance->roundStyle((string) $options['round_style']);
        }
        if (isset($options['label'])) {
            $label = (string) $options['label'];
            $instance->label($label, (int) ($options['label_font_size'] ?? 16), isset($options['label_font']) ? (string) $options['label_font'] : null);
        }
        if (isset($options['logo'])) {
            $instance->logo((string) $options['logo'], (float) ($options['logo_scale'] ?? 0.3));
        }
        if (isset($options['encoding'])) {
            $instance->encoding((string) $options['encoding']);
        }
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
            match ($format) {
                'svg' => $instance->asSvg(),
                'webp' => $instance->asWebP(),
                'eps' => $instance->asEps(),
                default => $instance->asPng(),
            };
        }

        return $instance;
    }

    public static function url(string $url, int $size = 300): string
    {
        return (new self($url))->size($size)->toDataUri();
    }

    public static function wifi(string $ssid, #[\SensitiveParameter] string $password, string $encryption = 'WPA', bool $hidden = false): self
    {
        $wifiString = sprintf('WIFI:T:%s;S:%s;P:%s;H:%s;;', $encryption, $ssid, $password, $hidden ? 'true' : 'false');
        return new self($wifiString);
    }

    public static function vcard(array $contact, string $firstName, string $lastName): self
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\n";
        $vcard .= "N:{$lastName};{$firstName};;;\n";
        $vcard .= "FN:{$firstName} {$lastName}\n";

        if (isset($contact['phone'])) {
            $vcard .= "TEL:{$contact['phone']}\n";
        }
        if (isset($contact['email'])) {
            $vcard .= "EMAIL:{$contact['email']}\n";
        }
        if (isset($contact['org'])) {
            $vcard .= "ORG:{$contact['org']}\n";
        }
        if (isset($contact['title'])) {
            $vcard .= "TITLE:{$contact['title']}\n";
        }
        if (isset($contact['url'])) {
            $vcard .= "URL:{$contact['url']}\n";
        }
        if (isset($contact['address'])) {
            $vcard .= "ADR:;;{$contact['address']};;;;\n";
        }

        $vcard .= "END:VCARD";
        return new self($vcard);
    }

    public static function email(string $email, string $subject = '', string $body = ''): self
    {
        $mailto = "mailto:{$email}";
        if ($subject || $body) {
            $params = [];
            if ($subject) {
                $params[] = 'subject=' . rawurlencode($subject);
            }
            if ($body) {
                $params[] = 'body=' . rawurlencode($body);
            }
            $mailto .= '?' . implode('&', $params);
        }
        return new self($mailto);
    }

    public static function phone(string $phone): self
    {
        return new self("tel:{$phone}");
    }

    public static function sms(string $phone, string $message = ''): self
    {
        $sms = "smsto:{$phone}";
        if ($message) {
            $sms .= ":{$message}";
        }
        return new self($sms);
    }

    public static function geo(float $lat, float $lon, string $label = ''): self
    {
        $geo = "geo:{$lat},{$lon}";
        if ($label) {
            $geo .= "?q=" . rawurlencode($label);
        }
        return new self($geo);
    }

    public static function bitcoin(string $address, float $amount = 0, string $label = ''): self
    {
        $btc = "bitcoin:{$address}";
        $params = [];
        if ($amount > 0) {
            $params[] = 'amount=' . $amount;
        }
        if ($label) {
            $params[] = 'label=' . rawurlencode($label);
        }
        if ($params) {
            $btc .= '?' . implode('&', $params);
        }
        return new self($btc);
    }

    public static function event(string $title, string $start, string $end, string $location = '', string $description = ''): self
    {
        $event = "BEGIN:VEVENT\n";
        $event .= "SUMMARY:{$title}\n";
        $event .= "DTSTART:{$start}\n";
        $event .= "DTEND:{$end}\n";
        if ($location) {
            $event .= "LOCATION:{$location}\n";
        }
        if ($description) {
            $event .= "DESCRIPTION:{$description}\n";
        }
        $event .= "END:VEVENT";
        return new self($event);
    }

    public static function color(int $red, int $green, int $blue): ColorInterface
    {
        return new Color(
            max(0, min(255, $red)),
            max(0, min(255, $green)),
            max(0, min(255, $blue))
        );
    }
}
