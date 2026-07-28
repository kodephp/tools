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
}
