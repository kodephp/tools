<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Time\Time;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Time\Time
 */
final class TimeTest extends TestCase
{
    public function testNowAndToday(): void
    {
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', Time::now());
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', Time::today());
    }

    public function testDiffForHumans(): void
    {
        $past = time() - 120;
        self::assertSame('2分钟前', Time::diffForHumans($past));

        $future = time() + 3600;
        self::assertSame('1小时后', Time::diffForHumans($future));
    }

    public function testWeekStartAndEnd(): void
    {
        $start = Time::weekStart();
        $end = Time::weekEnd();

        self::assertIsInt($start);
        self::assertIsInt($end);
        self::assertGreaterThanOrEqual($start, $end);
    }

    public function testIsToday(): void
    {
        self::assertTrue(Time::isToday(time()));
        self::assertFalse(Time::isToday(time() - 86400));
    }
}
