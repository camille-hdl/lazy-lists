<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\filter;

class FilterTest extends TestCase
{
    public function test()
    {

        $list = [1, 11];
        $fn = static function ($v) {
            return $v > 10;
        };
        $this->assertSame([11], filter($fn, $list));
    }

    public function testPassNoCollection()
    {
        $this->expectException(\TypeError::class);
        filter(function () {
            return "";
        }, 'invalidCollection');
    }

    public function testPassNonCallable()
    {
        $this->expectException(\TypeError::class);
        filter('notACallable', []);
    }
}
