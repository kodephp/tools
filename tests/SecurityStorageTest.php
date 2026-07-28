<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Security\Contracts\RateLimiterStorageInterface;
use Kode\Security\Security;
use Kode\Security\Storage\ApcuStorage;
use Kode\Security\Storage\FileStorage;
use Kode\Security\Storage\MemoryStorage;
use Kode\Security\Storage\RedisStorage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Security\Storage\FileStorage
 * @covers \Kode\Security\Storage\MemoryStorage
 * @covers \Kode\Security\Security
 */
final class SecurityStorageTest extends TestCase
{
    protected function setUp(): void
    {
        MemoryStorage::flush();
        Security::setAutoSession(false);
    }

    protected function tearDown(): void
    {
        MemoryStorage::flush();
    }

    public function testMemoryStorageHitAndRemaining(): void
    {
        $storage = new MemoryStorage();

        self::assertSame(5, $storage->hit('key', 5, 60));
        self::assertSame(4, $storage->hit('key', 5, 60));
        self::assertSame(3, $storage->remaining('key', 5, 60));
        self::assertSame(0, $storage->hit('key', 2, 60));
    }

    public function testMemoryStorageReset(): void
    {
        $storage = new MemoryStorage();
        $storage->hit('reset_key', 5, 60);

        self::assertTrue($storage->reset('reset_key'));
        self::assertSame(5, $storage->remaining('reset_key', 5, 60));
    }

    public function testFileStorageHit(): void
    {
        $dir = sys_get_temp_dir() . '/kode_storage_test_' . uniqid();
        $storage = new FileStorage($dir);

        self::assertSame(3, $storage->hit('file_key', 3, 60));
        self::assertSame(2, $storage->hit('file_key', 3, 60));
        self::assertSame(1, $storage->remaining('file_key', 3, 60));

        $storage->reset('file_key');
        self::assertTrue(is_dir($dir));

        $files = glob($dir . '/*');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        rmdir($dir);
    }

    public function testApcuStorageAvailability(): void
    {
        $storage = new ApcuStorage();
        self::assertIsBool($storage->available());
    }

    public function testRedisStorageAvailability(): void
    {
        self::assertIsBool((new RedisStorage(new \Redis(), 'test:'))->available());
    }

    public function testSecurityCanSwitchStorage(): void
    {
        $memory = new MemoryStorage();
        Security::setRateLimiterStorage($memory);

        self::assertTrue(Security::rateLimit('switch_user', 2, 60));
        self::assertTrue(Security::rateLimit('switch_user', 2, 60));
        self::assertFalse(Security::rateLimit('switch_user', 2, 60));

        self::assertSame(1, Security::rateLimitAvailable('other_user', 1, 60));
        self::assertInstanceOf(RateLimiterStorageInterface::class, Security::getRateLimiterStorage());
    }
}
