<?php

declare(strict_types=1);

namespace LazyLists\Test;

use function LazyLists\each;

class EachTest extends TestCase
{
    public function test()
    {

        $list = ['value', 'value'];
        $c = 0;
        $fn = function ($v) use (&$c) {
            $c++;
        };
        each($fn, $list);
        $this->assertEquals(2, $c);
    }

    public function testPassNoCollection()
    {
        $this->expectException(\TypeError::class);
        each(function () {
            return;
        }, 'invalidCollection');
    }

    public function testPassNonCallable()
    {
        $this->expectException(\TypeError::class);
        each('notACallable', []);
    }
}
