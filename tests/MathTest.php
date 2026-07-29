<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Math\Math;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Math\Math
 */
final class MathTest extends TestCase
{
    protected function setUp(): void
    {
        Math::setDefaultScale(10);
    }

    public function testDefaultScale(): void
    {
        self::assertSame(10, Math::getDefaultScale());
        Math::setDefaultScale(4);
        self::assertSame(4, Math::getDefaultScale());
        Math::setDefaultScale(-1);
        self::assertSame(0, Math::getDefaultScale());
    }

    public function testAddWithScale(): void
    {
        self::assertSame('3.3000000000', Math::add('1.1', '2.2'));
        self::assertSame('3.3', Math::add('1.1', '2.2', 1));
        self::assertSame('3.30', Math::add('1.1', '2.2', 2));
    }

    public function testSubWithScale(): void
    {
        self::assertSame('1.1000000000', Math::sub('3.3', '2.2'));
        self::assertSame('1.1', Math::sub('3.3', '2.2', 1));
    }

    public function testMulWithScale(): void
    {
        self::assertSame('6.0500000000', Math::mul('2.2', '2.75'));
        self::assertSame('6.05', Math::mul('2.2', '2.75', 2));
    }

    public function testDivWithScale(): void
    {
        self::assertSame('0.3333333333', Math::div('1', '3'));
        self::assertSame('0.33', Math::div('1', '3', 2));
    }

    public function testDivByZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Math::div('1', '0');
    }

    public function testPowAndSqrt(): void
    {
        self::assertSame('8.0000000000', Math::pow('2', 3));
        self::assertSame('8.00', Math::pow('2', 3, 2));
        self::assertSame('3.0000000000', Math::sqrt('9'));
        self::assertSame('3.00', Math::sqrt('9', 2));
    }

    public function testFinancialCalculations(): void
    {
        self::assertSame('80.00', Math::discount('100', '0.8'));
        self::assertSame('13.00', Math::tax('100', '0.13'));
        self::assertSame('50.00', Math::percentage('50', '100'));
        self::assertSame('110.00', Math::taxIncluded('100', '0.1'));
        self::assertSame('90.90', Math::taxExcluded('100', '0.1'));
        self::assertSame('10.00', Math::simpleInterest('100', '0.05', 2));
    }

    public function testStatistics(): void
    {
        self::assertSame('3.0000000000', Math::average([1, 2, 3, 4, 5]));
        self::assertSame('3.0000000000', Math::median([1, 2, 3, 4, 5]));
        self::assertSame('3.5000000000', Math::median([1, 2, 3, 4, 5, 6]));
        self::assertSame(2, Math::mode([1, 2, 2, 3]));
        self::assertSame('0', Math::standardDeviation([5]));
        self::assertEqualsWithDelta('1.5811', Math::standardDeviation([1, 2, 3, 4, 5], 4), 0.0001);
    }

    public function testCompare(): void
    {
        self::assertSame(1, Math::compare('2', '1'));
        self::assertSame(0, Math::compare('1.0', '1'));
        self::assertSame(-1, Math::compare('1', '2'));
        self::assertTrue(Math::equal('1.00', '1.0'));
    }
}
