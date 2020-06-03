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
    public function testNull()
    {
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity);
        $this->assertSame([1, null, 2], $pipe2([1, null, 2]));
    }
    public function testEmpty()
    {
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity);
        $this->assertNull($pipe2([]));
    }
    public function testOneValue()
    {
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity);
        $this->assertSame([null, 2], $pipe2([null, 2]));
    }
}
