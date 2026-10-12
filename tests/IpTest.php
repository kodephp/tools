<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Ip\Ip;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Ip\Ip
 */
final class IpTest extends TestCase
{
    public function testInCidr(): void
    {
        self::assertTrue(Ip::inCidr('192.168.1.1', '192.168.0.0/16'));
        self::assertTrue(Ip::inCidr('192.168.1.1', '192.168.1.0/24'));
        self::assertFalse(Ip::inCidr('10.0.0.1', '192.168.0.0/16'));
    }

    public function testInRange(): void
    {
        self::assertTrue(Ip::inRange('192.168.1.1', '192.168.0.0/16'));
        self::assertTrue(Ip::inRange('192.168.1.1', '192.168.1.0-192.168.1.255'));
        self::assertFalse(Ip::inRange('10.0.0.1', '192.168.0.0/16'));
    }

    public function testIsPrivateAndPublic(): void
    {
        self::assertTrue(Ip::isPrivate('192.168.1.1'));
        self::assertFalse(Ip::isPublic('192.168.1.1'));
        self::assertTrue(Ip::isPublic('8.8.8.8'));
    }

    public function testGetVersion(): void
    {
        self::assertSame(4, Ip::getVersion('192.168.1.1'));
        self::assertSame(6, Ip::getVersion('::1'));
        self::assertNull(Ip::getVersion('invalid'));
    }

    public function testDecodeLocationResponseMapsSuccessPayload(): void
    {
        $body = json_encode([
            'status' => 'success',
            'country' => 'China',
            'countryCode' => 'CN',
            'regionName' => 'Guangdong',
            'region' => 'GD',
            'city' => 'Shenzhen',
            'timezone' => 'Asia/Shanghai',
            'isp' => 'Example ISP',
        ]);

        $loc = Ip::decodeLocationResponse('8.8.8.8', $body);

        self::assertIsArray($loc);
        self::assertSame('8.8.8.8', $loc['ip']);
        self::assertSame('China', $loc['country']);
        self::assertSame('CN', $loc['countryCode']);
        self::assertSame('Guangdong', $loc['region']);
        self::assertSame('GD', $loc['regionCode']);
        self::assertSame('Shenzhen', $loc['city']);
        self::assertNull($loc['zip']);
    }

    public function testDecodeLocationResponseRejectsMalformedBodies(): void
    {
        // 非 JSON
        self::assertNull(Ip::decodeLocationResponse('1.1.1.1', '<html>502</html>'));
        // JSON 但顶层是字符串
        self::assertNull(Ip::decodeLocationResponse('1.1.1.1', '"ok"'));
        // JSON 列表（顶层非关联数组）
        self::assertNull(Ip::decodeLocationResponse('1.1.1.1', '[1,2,3]'));
        // 缺 status 字段
        self::assertNull(Ip::decodeLocationResponse('1.1.1.1', '{"country":"X"}'));
        // status 非 success
        self::assertNull(Ip::decodeLocationResponse('1.1.1.1', '{"status":"fail","message":"private range"}'));
    }

    public function testGetLocationRejectsInvalidAndPrivateIps(): void
    {
        self::assertNull(Ip::getLocation('not-an-ip'));
        self::assertNull(Ip::getLocation('192.168.1.1'));
        self::assertNull(Ip::getLocation('127.0.0.1'));
    }
}
