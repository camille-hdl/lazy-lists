<?php

declare(strict_types=1);

namespace LazyLists\Test;

use function LazyLists\map;
use function LazyLists\filter;
use function LazyLists\pipe;
use function LazyLists\flatten;
use function LazyLists\reduce;

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
        $isGreaterThanOrEqual20 = static function ($v) {
            return $v >= 20;
        };
        $pipe = pipe(
            map($fn),
            filter($isGreaterThanOrEqual20),
            map($fn2)
        );
        $this->assertSame([21, 31], $pipe($list));
    }

    public function testFlatten()
    {
        $list = [1, 2, [[3, 4]]];
        $fn = static function ($v) {
            return $v * 10;
        };
        $fn2 = static function ($v) {
            return $v + 1;
        };
        $isGreaterThanOrEqual20 = static function ($v) {
            return $v >= 20;
        };
        $splitInCouples = static function ($v) {
            return [$v - 10, $v];
        };
        $pipe = pipe(
            flatten(2),
            map($fn),
            filter($isGreaterThanOrEqual20),
            map($fn2),
            map($splitInCouples),
            flatten(1)
        );
        $this->assertSame([11, 21, 21, 31, 31, 41], $pipe($list));
    }


    public function testFlattenReduce()
    {
        $list = [1, 2, [[3, 4]]];
        $plus10 = static function ($v) {
            return $v * 10;
        };
        $plus1 = static function ($v) {
            return $v + 1;
        };
        $isGreaterThanOrEqual20 = static function ($v) {
            return $v >= 20;
        };
        $sum = static function ($acc, $num) {
            return $acc + $num;
        };
        $pipe = pipe(
            flatten(2),
            map($plus10),
            filter($isGreaterThanOrEqual20),
            map($plus1),
            reduce($sum, 0)
        );
        $this->assertEquals(93, $pipe($list));
    }
}
