<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\map;
use function LazyLists\filter;
use function LazyLists\pipe;

class MapTest extends TestCase
{
    public function test()
    {
        $times2 = map(static function ($v) {
            return $v * 2;
        });
        $pipe2 = pipe($times2);
        $this->assertSame([2, 4, 6], $pipe2([1, 2, 3]));
    }
}
