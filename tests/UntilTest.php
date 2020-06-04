<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\until;

class UntilTest extends TestCase
{
    public function test()
    {

        $list = [1, 2, 11];
        $fn = static function ($v) {
            return $v > 10;
        };
        $this->assertSame([1, 2], until($fn, $list));
    }

    public function testPassNoCollection()
    {
        $this->expectException(\Exception::class);
        until(function () {
            return false;
        }, 'invalidCollection');
    }

    public function testPassNonCallable()
    {
        $this->expectException(\TypeError::class);
        until('notACallable', []);
    }
}
