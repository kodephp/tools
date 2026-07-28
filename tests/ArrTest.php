<?php

declare(strict_types=1);

namespace Kode\Tests;

use Kode\Array\Arr;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kode\Array\Arr
 */
final class ArrTest extends TestCase
{
    public function testPluckAlias(): void
    {
        $data = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        self::assertSame(['Alice', 'Bob'], Arr::pluck($data, 'name'));
        self::assertSame([1 => 'Alice', 2 => 'Bob'], Arr::pluck($data, 'name', 'id'));
    }

    public function testToTreeAndFromTree(): void
    {
        $list = [
            ['id' => 1, 'parent_id' => 0, 'name' => 'Root'],
            ['id' => 2, 'parent_id' => 1, 'name' => 'Child'],
            ['id' => 3, 'parent_id' => 1, 'name' => 'Child 2'],
        ];

        $tree = Arr::toTree($list);
        self::assertCount(1, $tree);
        self::assertCount(2, $tree[0]['children']);

        $flat = Arr::fromTree($tree);
        self::assertCount(3, $flat);
    }

    public function testFirstAndLast(): void
    {
        self::assertSame(1, Arr::first([1, 2, 3]));
        self::assertSame(3, Arr::last([1, 2, 3]));
        self::assertNull(Arr::first([]));
    }

    public function testFind(): void
    {
        $found = Arr::find([1, 2, 3], static fn (int $n): bool => $n > 1);
        self::assertSame(2, $found);
    }

    public function testAnyAndAll(): void
    {
        self::assertTrue(Arr::any([1, 2, 3], static fn (int $n): bool => $n > 2));
        self::assertFalse(Arr::all([1, 2, 3], static fn (int $n): bool => $n > 2));
    }
}
