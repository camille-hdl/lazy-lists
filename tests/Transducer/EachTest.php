<?php

declare(strict_types=1);

namespace LazyLists\Test\Transducer;

use LazyLists\Test\TestCase;

use function LazyLists\each;
use function LazyLists\filter;
use function LazyLists\pipe;

class EachTest extends TestCase
{
    public function test()
    {
        $log = [];
        $logValue = each(function ($v) use (&$log) {
            $log[] = $v;
        });
        $pipe2 = pipe($logValue);
        $this->assertSame([1, 2, 3], $pipe2([1, 2, 3]));
    }
}
