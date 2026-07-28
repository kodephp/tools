<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Security\Security;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Security\Security
 */
final class SecurityTest extends TestCase
{
    private string $rateDir;

    protected function setUp(): void
    {
        $this->rateDir = sys_get_temp_dir() . '/kode_test_rate_' . uniqid();
        Security::setRateLimitDir($this->rateDir);
        Security::setAutoSession(false);
    }

    protected function tearDown(): void
    {
        $files = glob($this->rateDir . '/*');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        if (is_dir($this->rateDir)) {
            rmdir($this->rateDir);
        }
    }

    public function testRateLimitAllowsRequests(): void
    {
        self::assertTrue(Security::rateLimit('user_1', 2, 60));
        self::assertTrue(Security::rateLimit('user_1', 2, 60));
        self::assertFalse(Security::rateLimit('user_1', 2, 60));
    }

    public function testRateLimitRemaining(): void
    {
        self::assertSame(2, Security::rateLimitRemaining('user_2', 2, 60));
        self::assertSame(1, Security::rateLimitRemaining('user_2', 2, 60));
        self::assertSame(0, Security::rateLimitRemaining('user_2', 2, 60));
    }

    public function testRateLimitReset(): void
    {
        Security::rateLimit('user_3', 1, 60);
        self::assertFalse(Security::rateLimit('user_3', 1, 60));
        self::assertTrue(Security::rateLimitReset('user_3'));
        self::assertTrue(Security::rateLimit('user_3', 1, 60));
    }

    public function testSignAndVerify(): void
    {
        $secret = 'this_is_a_very_long_secret_key';
        $data = ['user_id' => 1, 'action' => 'pay'];

        $signed = Security::signPayload($data, $secret);

        self::assertArrayHasKey('_time', $signed);
        self::assertArrayHasKey('_sign', $signed);
        self::assertTrue(Security::signVerify($signed, $secret));
    }

    public function testSignVerifyFailsWithWrongSecret(): void
    {
        $secret = 'this_is_a_very_long_secret_key';
        $signed = Security::signPayload(['id' => 1], $secret);

        self::assertFalse(Security::signVerify($signed, 'wrong_secret_key_xxxxxxxxxxxx'));
    }

    public function testSignVerifyFailsWhenExpired(): void
    {
        $secret = 'this_is_a_very_long_secret_key';
        $signed = Security::signPayload(['id' => 1], $secret);
        $signed['_time'] = time() - 600;

        self::assertFalse(Security::signVerify($signed, $secret, 300));
    }

    public function testCsrfTokenGeneration(): void
    {
        $token = Security::csrfToken('test_key');

        self::assertSame(32, strlen($token));
        self::assertTrue(Security::csrfVerify($token, 'test_key'));
    }

    public function testCsrfVerifyOnce(): void
    {
        $token = Security::csrfTokenOnce('once_key');

        self::assertTrue(Security::csrfVerifyOnce($token, 'once_key'));
        self::assertFalse(Security::csrfVerifyOnce($token, 'once_key'));
    }

    public function testInCidr(): void
    {
        self::assertTrue(Security::inCidr('192.168.1.1', '192.168.0.0/16'));
        self::assertFalse(Security::inCidr('10.0.0.1', '192.168.0.0/16'));
    }

    public function testInRange(): void
    {
        self::assertTrue(Security::inRange('192.168.1.1', '192.168.0.0/16'));
        self::assertTrue(Security::inRange('192.168.1.1', '192.168.1.0-192.168.1.255'));
        self::assertFalse(Security::inRange('10.0.0.1', '192.168.0.0/16'));
    }

    public function testRandomToken(): void
    {
        $token = Security::randomToken(32);
        self::assertSame(32, strlen($token));
    }

    public function testInputCasting(): void
    {
        $_POST['age'] = '25';
        $_POST['name'] = '<script>alert(1)</script>';

        self::assertSame(25, Security::input('age', null, 'int', 'post'));
        self::assertSame('guest', Security::input('missing', 'guest', 'string', 'post'));
    }

    public function testInputsBatch(): void
    {
        $_GET['page'] = '2';
        $_GET['size'] = '10';

        $result = Security::inputs([
            ['page', 'int', 1, 'get'],
            ['size', 'int', 20, 'get'],
        ]);

        self::assertSame(['page' => 2, 'size' => 10], $result);
    }

    public function testXssClean(): void
    {
        $dirty = '<script>alert("xss")</script>hello';
        $clean = Security::xssClean($dirty);

        self::assertStringNotContainsString('<script>', $clean);
        self::assertStringContainsString('hello', $clean);
    }
}
