<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\map;
use function LazyLists\take;
use function LazyLists\flatten;
use function LazyLists\pipe;

class TakeTest extends TestCase
{
    public function test()
    {
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe($identity, take(2));
        $this->assertSame([1, 2], $pipe2([1, 2, 3]));
        $this->assertSame([1], $pipe2([1]));
    }
    public function testSingleTransducer()
    {
        $pipe2 = pipe(take(2));
        $this->assertSame([1, 2], $pipe2([1, 2, 3]));
        $this->assertSame([1], $pipe2([1]));
    }
    public function testLastTransducer()
    {
        $identity = map(static function ($v) {
            return $v;
        });
        $pipe2 = pipe(take(2), $identity);
        $this->assertSame([1, 2], $pipe2([1, 2, 3]));
        $this->assertSame([1], $pipe2([1]));
    }
    public function testFlatten()
    {
        $pipe2 = pipe(flatten(1), take(2));
        $this->assertSame([1, 2], $pipe2([[1, 2], 3]));
    }
}
