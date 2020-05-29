<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\map;

class MapTest extends TestCase
{
    public function test()
    {

        $list = ['value', 'value'];
        $listIterator = new ArrayIterator($list);
        $hash = ['k1' => 'val1', 'k2' => 'val2'];
        $hashIterator = new ArrayIterator($hash);
        $fn = static function ($v, $k) {
            return $k . $v;
        };
        $this->assertSame(['0value', '1value'], map($fn, $list));
        $this->assertSame(['0value', '1value'], map($fn, $listIterator));
        $this->assertSame(['k1val1', 'k2val2'], map($fn, $hash));
        $this->assertSame(['k1val1', 'k2val2'], map($fn, $hashIterator));
    }

    public function testPassNoCollection()
    {
        $this->expectException(\Exception::class);
        map(function () {
            return "";
        }, 'invalidCollection');
    }

    public function testPassNonCallable()
    {
        $this->expectException(\TypeError::class);
        map('notACallable', []);
    }
}
