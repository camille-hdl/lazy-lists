<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\map;
use function LazyLists\until;
use function LazyLists\pipe;

class UntilTest extends TestCase
{
    public function test()
    {
        $superiorThan1 = until(static function ($v) {
            return $v > 1;
        });
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity, $superiorThan1, $identity);
        $this->assertSame([1], $pipe2([1, 2, 3]));
    }

    public function testLastTransducer()
    {
        $superiorThan1 = until(static function ($v) {
            return $v > 1;
        });
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe = pipe($superiorThan1);
        $this->assertSame([1], $pipe([1, 2, 3]));
    }
    public function testEmptyList()
    {
        $superiorThan1 = until(static function ($v) {
            return $v > 1;
        });
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe = pipe($superiorThan1);
        $this->assertSame([], $pipe([2, 2, 3]));
    }
}
