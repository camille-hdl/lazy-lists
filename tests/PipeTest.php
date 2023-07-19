<?php

declare(strict_types=1);

namespace LazyLists\Test;

use function LazyLists\map;
use function LazyLists\filter;
use function LazyLists\pipe;
use function LazyLists\flatten;
use function LazyLists\reduce;
use function LazyLists\take;
use function LazyLists\each;
use function LazyLists\until;

/**
 * "integration" tests
 */
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

    public function testTake()
    {
        $list = [1, 2, 3, 4, 5];
        $pipe = pipe(
            take(2)
        );
        $this->assertSame([1, 2], $pipe($list));
    }
    public function testFlattenTakeMap()
    {
        $list = [
            ["a", "b"],
            ["c", "d"]
        ];
        $pipe = pipe(
            flatten(1),
            take(3),
            map(static function ($v) {
                return \strtoupper($v);
            })
        );
        $this->assertSame(["A", "B", "C"], $pipe($list));
    }
    public function testDirectoryIterator()
    {
        $directory = new \DirectoryIterator(__DIR__ . '/testFiles');
        $noDotFile = static function ($file) {
            return !$file->isDot();
        };
        $toJSONArray = static function ($file) {
            return json_decode($file->openFile()->fgets(), true);
        };
        $pipe = pipe(
            filter($noDotFile),
            map($toJSONArray),
            map(static function ($v) {
                return $v["a"];
            })
        );
        $output = $pipe($directory);
        sort($output);
        $this->assertSame([1, 2, 3], $output);
    }

    public function testInvalidTransducers()
    {
        $this->expectException(\TypeError::class);
        $pipe = pipe("notATransducer");
    }

    public function testRealUseCase()
    {
        $data1 = [
            ["a" => 1],
            ["a" => 11],
            ["a" => 2],
        ];
        $data2 = [
            ["a" => 22],
            ["a" => 15],
            ["a" => 3],
        ];
        $data3 = [
            ["a" => 3],
        ];
        $input = new ArrayIteratorSpy([
            \json_encode($data1),
            \json_encode($data2),
            \json_encode($data3),
        ]);
        $decode_count = 0;
        $decode = function ($i) use (&$decode_count) {
            $decode_count++;
            return \json_decode($i, true);
        };
        $getA_count = 0;
        $getA = function ($data) use (&$getA_count) {
            $getA_count++;
            return $data["a"];
        };
        $sup_count = 0;
        $supOrEq10 = function ($a) use (&$sup_count) {
            $sup_count++;
            return $a >= 10;
        };
        $sum_count = 0;
        $sum = function ($acc, $a) use (&$sum_count) {
            $sum_count++;
            return $acc + $a;
        };
        $expected = 48;
        $pipe = pipe(
            map($decode),
            flatten(1),
            map($getA),
            filter($supOrEq10),
            reduce($sum, 0)
        );
        $this->assertEquals($expected, $pipe($input));
        $this->assertEquals(4, $input->howManyNexts, "We should iterate over the input only once");
        $this->assertEquals(3, $decode_count);
        $this->assertEquals(7, $getA_count);
        $this->assertEquals(7, $sup_count);
        $this->assertEquals(3, $sum_count);
    }

    public function testFlattenEachUntil()
    {
        $list = [
            [1, 2],
            [3, 4]
        ];
        $c = 0;
        $pipe = pipe(
            flatten(1),
            each(function ($v) use (&$c) {
                $c = $c + $v;
            }),
            until(function ($v) use (&$c) {
                return $c > 6;
            })
        );
        $this->assertSame([1, 2, 3], $pipe($list));
    }
}
