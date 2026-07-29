<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\String\Str;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\String\Str
 */
final class StrTest extends TestCase
{
    public function testMbStrcut(): void
    {
        $str = '张三你吃了吗';
        self::assertSame('张三', Str::mbStrcut($str, 0, 6));
        self::assertSame('张三你', Str::mbStrcut($str, 0, 9));
    }

    public function testLimitLengthAlias(): void
    {
        $str = '这是一个很长的中文字符串';
        // mb_strimwidth 会把省略号计入限制宽度，10 宽度下保留 6 宽度中文 + 3 宽度省略号
        self::assertSame('这是一...', Str::limitLength($str, 10));
        self::assertSame('这是一个很...', Str::limitLength($str, 13));
    }

    public function testMaskPhone(): void
    {
        self::assertSame('138****8000', Str::maskPhone('13800138000'));
    }

    public function testCamelAndSnake(): void
    {
        self::assertSame('helloWorld', Str::camel('hello_world'));
        self::assertSame('hello_world', Str::snake('helloWorld'));
    }

    public function testTruncate(): void
    {
        self::assertSame('你好...', Str::truncate('你好吗世界', 2));
    }

    public function testMaskCustomRange(): void
    {
        // 手机号：保留前后 2 位
        self::assertSame('13*******00', Str::maskKeep('13800138000', 2, 2));
        // 从第 2 位开始替换到末尾
        self::assertSame('13*********', Str::mask('13800138000', 2, -1));
        // 自定义掩码字符
        self::assertSame('138####8000', Str::maskPhone('13800138000', 3, 4, '#'));
    }

    public function testMaskKeepUnicode(): void
    {
        self::assertSame('张****吗', Str::maskKeep('张三你吃了吗', 1, 1));
        self::assertSame('张三****', Str::maskKeep('张三你吃了吗', 2, 0));
    }

    public function testMaskEmailCustomRange(): void
    {
        self::assertSame('us**@example.com', Str::maskEmail('user@example.com', 2, 0));
        self::assertSame('u**r@example.com', Str::maskEmail('user@example.com', 1, 1));
    }

    public function testUuidAndCode(): void
    {
        $uuid = Str::uuid();
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid);

        $code = Str::code('XXXX-9999-AA');
        self::assertMatchesRegularExpression('/^[0-9A-F]{4}-[0-9]{4}-[A-Z]{2}$/', $code);

        $ordered = Str::orderedUuid();
        self::assertSame(18, strlen($ordered));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $ordered);
    }

    public function testUuidBatch(): void
    {
        $codes = Str::uuidBatch(5, '9999');
        self::assertCount(5, $codes);
        $codes = array_map('strval', $codes);
        self::assertSame(5, count(array_unique($codes)));
        foreach ($codes as $code) {
            self::assertMatchesRegularExpression('/^\d{4}$/', $code);
        }
    }

    public function testValidatePlate(): void
    {
        self::assertTrue(Str::validatePlate('京A12345'));
        self::assertTrue(Str::validatePlate('京A12345', Str::PLATE_OIL));
        self::assertTrue(Str::validatePlate('京AD12345', Str::PLATE_NEW_ENERGY));
        self::assertTrue(Str::validatePlate('京V12345', Str::PLATE_MILITARY));
        self::assertTrue(Str::validatePlate('使12345', Str::PLATE_EMBASSY));
        self::assertTrue(Str::validatePlate('领A1234', Str::PLATE_CONSULATE));
        self::assertTrue(Str::validatePlate('京A1234工', Str::PLATE_WORK));
        self::assertFalse(Str::validatePlate('京A123', Str::PLATE_OIL));

        // 白名单优先命中
        self::assertTrue(Str::validatePlate('INVALID', Str::PLATE_ALL, ['INVALID']));

        // 类型识别
        self::assertSame(Str::PLATE_NEW_ENERGY, Str::plateType('沪AF12345'));
    }
}
