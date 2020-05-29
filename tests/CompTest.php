<?php

declare(strict_types=1);

namespace LazyLists\Test;

use LazyLists\Comp;

class CompTest extends TestCase
{
    public function testGetHello()
    {
        $object = \Mockery::mock(Comp::class);
        $object->shouldReceive('getHello')->passthru();

        $this->assertSame('Hello, World!', $object->getHello());
    }
}
