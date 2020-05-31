<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\flatten;

class FlattenTest extends TestCase
{
    public function testLevel1()
    {

        $list = [
            [1, 2],
            [20, 40],
            [
                [100, 200]
            ]
        ];
        $expected = [
            1,
            2,
            20,
            40,
            [100, 200]
        ];
        $this->assertSame($expected, flatten(1, $list));
    }
    public function testLevel3()
    {

        $list = [
            [1, 2],
            [20, 40],
            [
                [100, 200],
                [
                    [200, 300],
                    [400, 500]
                ]
            ]
        ];
        $expected = [
            1,
            2,
            20,
            40,
            100,
            200,
            200,
            300,
            400,
            500
        ];
        $this->assertSame($expected, flatten(3, $list));
    }
}
