<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\map;
use function LazyLists\flatten;
use function LazyLists\pipe;
use function LazyLists\take;

class FlattenTest extends TestCase
{
    public function test()
    {
        $input = [
            [1, 2],
            [3, 4]
        ];
        $times2 = map(static function ($v) {
            return $v * 2;
        });
        $pipe = pipe(flatten(1), $times2);
        $this->assertSame([2, 4, 6, 8], $pipe($input));
    }
    public function testNull()
    {
        $input = [
            [1, null, 2]
        ];
        $pipe = pipe(flatten(1));
        $this->assertCount(3, $pipe($input));
        $this->assertSame([1, null, 2], $pipe($input));
    }
    public function testTake()
    {
        $input = [
            [1, null, 2]
        ];
        $pipe = pipe(flatten(1), take(2));
        $this->assertCount(2, $pipe($input));
        $this->assertSame([1, null], $pipe($input));
    }
    public function testAssociativeArrays()
    {
        $input = [
            [[1], ["a" => 1], 2]
        ];
        $pipe = pipe(flatten(2));
        $this->assertCount(3, $pipe($input));
        $this->assertSame([1, ["a" => 1], 2], $pipe($input));
    }
}
