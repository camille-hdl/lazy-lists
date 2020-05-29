<?php

declare(strict_types=1);

namespace LazyLists\Test;

use function LazyLists\map;
use function LazyLists\pipe;

class PipeTest extends TestCase
{
    public function test()
    {

        $list = [1, 2, 3];
        $fn = static function ($v) {
            return $v * 10;
        };
        $fn2 = static function ($v) {
            return $v + 1;
        };
        $pipe = pipe(
            map($fn),
            map($fn2)
        );
        $this->assertSame([11, 21, 31], $pipe($list));
    }
}
