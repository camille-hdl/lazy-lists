<?php

declare(strict_types=1);

namespace LazyLists\Test;

use ArrayIterator;

/**
 * For testing purposes only
 */
class ArrayIteratorSpy extends ArrayIterator
{
    public $howManyNexts = 0;
    public function next()
    {
        $this->howManyNexts++;
        parent::next();
    }
}
