<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

use function LazyLists\isAssociativeArray;

class IsAssociativeArrayTest extends TestCase
{
    public function testPositives()
    {
        $this->assertTrue(isAssociativeArray(["a" => "b"]));
        $this->assertTrue(isAssociativeArray(["a" => 1]));
        $this->assertTrue(isAssociativeArray(["a" => null]));
        $this->assertTrue(isAssociativeArray(["" => ""]));
        $this->assertTrue(isAssociativeArray(["_" => ["a"]]));
    }
    public function testNegatives()
    {
        $this->assertFalse(isAssociativeArray(["a", "b"]));
        $this->assertFalse(isAssociativeArray([]));
        $this->assertFalse(isAssociativeArray([1, 2]));
        $this->assertFalse(isAssociativeArray([["" => ""]]));
        $this->assertFalse(isAssociativeArray(true));
        $this->assertFalse(isAssociativeArray(false));
        $this->assertFalse(isAssociativeArray(new \stdClass()));
    }
}
