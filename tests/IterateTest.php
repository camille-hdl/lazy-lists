<?php

declare(strict_types=1);

namespace LazyLists\Test;

use function LazyLists\map;
use function LazyLists\filter;
use function LazyLists\iterate;
use function LazyLists\pipe;
use function LazyLists\flatten;
use function LazyLists\reduce;
use function LazyLists\take;
use function LazyLists\each;
use function LazyLists\until;

/**
 * "integration" tests
 */
class IterateTest extends TestCase
{
    public function test()
    {
        $list = [
            [1, 2],
            [3, 4]
        ];
        $pipe = pipe(
            flatten(1),
            map(function ($v) {
                return $v * 2;
            })
        );
        $this->assertSame([2, 4, 6, 8], $pipe($list), "reference");
        $generator = iterate(
            flatten(1),
            map(function ($v) {
                return $v * 2;
            })
        );
        $o = [];
        $o2 = [];
        $iterator = $generator($list);
        foreach ($iterator as $value) {
            $o[] = $value;
        }
        $this->assertSame([2, 4, 6, 8], $o);
        $iterator->rewind();
        foreach ($iterator as $value) {
            $o2[] = $value;
        }
        $this->assertSame([2, 4, 6, 8], $o2, "the iterator should satisfy rewind semantics");
    }
    public function testFilter()
    {
        $list = [
            1, 2, 10, 20, 30
        ];
        $generator = iterate(
            filter(static function ($v) {
                return $v >= 10;
            }),
            take(2)
        );
        $values = [];
        $keys = [];
        $iterator = $generator($list);
        foreach ($iterator as $key => $value) {
            $values[] = $value;
            $keys[] = $key;
        }
        $this->assertSame([10, 20], $values);
        $this->assertSame([0, 1], $keys);
    }
    public function testReduce()
    {
        $list = [
            1, 2, 10, 20, 30
        ];
        $generator = iterate(
            reduce(function ($acc, $v) {
                return $acc + $v;
            }, 0),
            until(static function ($sum) {
                return $sum > 40;
            })
        );
        $values = [];
        $iterator = $generator($list);
        foreach ($iterator as $key => $value) {
            $values[] = $value;
        }
        $this->assertSame([1, 3, 13, 33, 63], $values);
    }

    public function testReduceReadmeExample()
    {
        $reduceIterator = iterate(
            reduce(function ($acc, $v) {
                return $acc + $v;
            }, 0),
            until(function ($sum) {
                return $sum > 10;
            })
        );
        $output = [];
        foreach ($reduceIterator([1, 5, 10, 20]) as $reduction) {
            $output[] = $reduction;
        }
        $this->assertSame([1, 6, 16], $output);
    }
}
