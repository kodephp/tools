<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Crypto\Crypto;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Crypto\Crypto
 */
final class CryptoTest extends TestCase
{
    public function testMd5(): void
    {
        self::assertSame(md5('hello'), Crypto::md5('hello'));
        self::assertSame(md5('hello' . 'salt'), Crypto::md5('hello', 'salt'));
    }

    public function testCryptoMd5Alias(): void
    {
        self::assertSame(Crypto::md5('hello'), Crypto::cryptoMd5('hello'));
    }

    public function testPasswordHashAndVerify(): void
    {
        $hash = Crypto::passwordHash('password123');
        self::assertTrue(Crypto::passwordVerify('password123', $hash));
        self::assertFalse(Crypto::passwordVerify('wrong', $hash));
    }

    public function testHmac(): void
    {
        $key = 'this_is_a_very_long_key';
        $hmac = Crypto::hmac('data', $key);
        self::assertSame(64, strlen($hmac));
        self::assertSame($hmac, Crypto::cryptoHmac('data', $key));
    }

    public function testToken(): void
    {
        $token = Crypto::token(32);
        self::assertSame(32, strlen($token));

        $this->expectException(\InvalidArgumentException::class);
        Crypto::token(4);
    }

    public function testEncryptAndDecrypt(): void
    {
        $key = 'this_is_a_very_long_secret_key';
        $data = '敏感数据内容';

        $encrypted = (new Crypto($key))->encrypt($data);
        $decrypted = (new Crypto($key))->decrypt($encrypted);

        self::assertNotSame($data, $encrypted);
        self::assertSame($data, $decrypted);
    }

    public function testHashEquals(): void
    {
        self::assertTrue(Crypto::hashEquals('abc', 'abc'));
        self::assertFalse(Crypto::hashEquals('abc', 'def'));
    }
}
