<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Message\Message;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Message\Message
 */
final class MessageTest extends TestCase
{
    protected function setUp(): void
    {
        Message::clearCodes();
    }

    public function testDefaultResult(): void
    {
        $result = (new Message())->result();

        self::assertSame(200, $result['code']);
        self::assertSame('成功', $result['msg']);
        self::assertArrayNotHasKey('data', $result);
    }

    public function testStaticDefaultResult(): void
    {
        $result = Message::result();

        self::assertSame(200, $result['code']);
        self::assertSame('成功', $result['msg']);
    }

    public function testCodeAndMsg(): void
    {
        $result = Message::code(20001)->msg('请求数据有误')->result();

        self::assertSame(20001, $result['code']);
        self::assertSame('请求数据有误', $result['msg']);
    }

    public function testDataWithoutExplicitCode(): void
    {
        $result = Message::data(['id' => 1])->result();

        self::assertSame(200, $result['code']);
        self::assertSame('成功', $result['msg']);
        self::assertSame(['id' => 1], $result['data']);
    }

    public function testOrderIndependence(): void
    {
        $result = Message::data(['id' => 1])
            ->code(20001)
            ->msg('请求数据有误')
            ->page(1)
            ->name('张三')
            ->result();

        self::assertSame(20001, $result['code']);
        self::assertSame('请求数据有误', $result['msg']);
        self::assertSame(['id' => 1], $result['data']);
        self::assertSame(1, $result['page']);
        self::assertSame('张三', $result['name']);
    }

    public function testDelegatedStringMethod(): void
    {
        $result = Message::mbStrcut('张三你吃了吗', 0, 6)->result();

        self::assertSame(200, $result['code']);
        self::assertSame('张三', $result['mbStrcut']);
    }

    public function testNoDataWhenNotSet(): void
    {
        $result = Message::code(400)->msg('错误')->result();

        self::assertArrayNotHasKey('data', $result);
    }

    public function testCustomCodes(): void
    {
        Message::codes([900000 => '权限不足']);

        $result = Message::code(900000)->result();

        self::assertSame('权限不足', $result['msg']);
    }

    public function testStaticAndInstanceEquivalence(): void
    {
        $staticResult = Message::data(['id' => 1])->result();
        $instanceResult = (new Message())->data(['id' => 1])->result();

        self::assertSame($staticResult, $instanceResult);
    }

    public function testConcurrencySafety(): void
    {
        $first = Message::code(201)->result();
        $second = Message::code(202)->result();

        self::assertSame(201, $first['code']);
        self::assertSame(202, $second['code']);
    }

    public function testToJson(): void
    {
        $json = Message::code(200)->data(['id' => 1])->toJson();

        self::assertJson($json);
        $decoded = json_decode($json, true);
        self::assertSame(200, $decoded['code']);
    }

    public function testForbiddenMethodThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Message::exec('id');
    }
}
