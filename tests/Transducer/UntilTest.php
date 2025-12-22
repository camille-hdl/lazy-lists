<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\flatten;
use function LazyLists\iterate;
use function LazyLists\map;
use function LazyLists\until;
use function LazyLists\pipe;
use function LazyLists\reduce;

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
        $this->assertSame([1, 2], $pipe2([1, 2, 3]));
    }

    public function testLastTransducer()
    {
        $superiorThan1 = until(static function ($v) {
            return $v > 1;
        });
        $pipe = pipe($superiorThan1);
        $this->assertSame([1, 2], $pipe([1, 2, 3]));
    }

    public function testConditionMetImmediately()
    {
        $superiorThan1 = until(static function ($v) {
            return $v > 1;
        });
        $pipe = pipe($superiorThan1);
        $this->assertSame([2], $pipe([2, 2, 3]));
    }

    public function testDoesNotIterateTooMuch()
    {
        $testValues = [
            "a", "b", "c", "d", "e", "f", "g"
        ];
        $lettersTested = [];
        $isGreaterThanC = until(function ($v) use (&$lettersTested) {
            $lettersTested[] = $v;
            return $v >= "c";
        });
        $lettersUpperCased = [];
        $toUpperCase = map(function ($v) use (&$lettersUpperCased) {
            $lettersUpperCased[] = \mb_strtoupper($v);
            return \mb_strtoupper($v);
        });
        $reductionCalled = 0;
        $append = reduce(function (string $acc, string $v) use (&$reductionCalled): string {
            $reductionCalled++;
            return $acc . $v;
        }, "");
        $pipe = pipe(
            $isGreaterThanC,
            $toUpperCase,
            $append,
        );
        $output = $pipe($testValues);
        $this->assertSame("ABC", $output);
        $this->assertSame(["a", "b", "c"], $lettersTested);
        $this->assertSame(["A", "B", "C"], $lettersUpperCased);
        $this->assertEquals(3, $reductionCalled);
    }

    public function testFlattenedValues()
    {
        $testValues = [
            ["a", 1],
            ["b", 2],
            ["c", 3],
            ["d", 4],
            ["e", 5],
        ];
        $lettersTested = [];
        $isGreaterThanC = until(function ($v) use (&$lettersTested) {
            $lettersTested[] = $v[0];
            return $v[0] >= "c";
        });
        $reductionCalled = 0;
        $append = reduce(function (string $acc, mixed $v) use (&$reductionCalled): string {
            $reductionCalled++;
            return $acc . $v;
        }, "");
        $pipe = pipe(
            $isGreaterThanC,
            flatten(1),
            $append,
        );
        $output = $pipe($testValues);
        $this->assertSame("a1b2c3", $output);
        $this->assertSame(["a", "b", "c"], $lettersTested);
        $this->assertEquals(6, $reductionCalled);

        $iterator = iterate(
            $isGreaterThanC,
            flatten(1),
        )($testValues);
        $o = [];
        foreach ($iterator as $value) {
            $o[] = $value;
        }
        $this->assertSame(["a", 1, "b", 2, "c", 3], $o);
    }

    public function testGenerator()
    {
        $generator = (function () {
            $testValues = [
                "a", "b", "c", "d", "e", "f", "g"
            ];
            foreach ($testValues as $v) {
                yield $v;
            }
        });
        $isC = static function ($v) {
            return $v === "c";
        };
        $pipe = pipe(
            until($isC),
        );
        $output = $pipe($generator);
        $this->assertSame(["a", "b", "c"], $output);
    }
}
