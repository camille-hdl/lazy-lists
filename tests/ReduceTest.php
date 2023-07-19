<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\reduce;

class ReduceTest extends TestCase
{
    public function test()
    {
        $list = [1, 11];
        $fn = static function ($acc, $v) {
            return $acc . "-" . $v;
        };
        $this->assertSame("-1-11", reduce($fn, "", $list));
    }
    public function testPassNoCollection()
    {
        $this->expectException(\TypeError::class);
        reduce(function () {
            return "";
        }, "", 'invalidCollection');
    }

    public function testPassNonCallable()
    {
        $this->expectException(\TypeError::class);
        reduce('notACallable', "", []);
    }
}
