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
}
