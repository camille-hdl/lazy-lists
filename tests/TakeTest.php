<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\take;

class TakeTest extends TestCase
{
    public function test()
    {

        $list = [1, 2, 3, 4];
        $this->assertSame([1, 2], take(2, $list));
        $this->assertSame([1, 2, 3, 4], take(5, $list));
        $this->assertSame([], take(0, $list));
    }

    public function testPassNoCollection()
    {
        $this->expectException(\Exception::class);
        take(1, "invalidCollection");
    }
}
