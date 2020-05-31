<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\map;
use function LazyLists\filter;
use function LazyLists\pipe;

class FilterTest extends TestCase
{
    public function test()
    {
        $superiorThan1 = filter(static function ($v) {
            return $v > 1;
        });
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity, $superiorThan1, $identity);
        $this->assertSame([2, 3], $pipe2([1, 2, 3]));
    }

    public function testLastTransducer()
    {
        $superiorThan1 = filter(static function ($v) {
            return $v > 1;
        });
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe = pipe($superiorThan1);
        $this->assertSame([2, 3], $pipe([1, 2, 3]));
    }
}
