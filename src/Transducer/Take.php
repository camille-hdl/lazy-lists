<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camille.hodoul@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace LazyLists\Transducer;

use LazyLists\LazyIterator;

/**
 *
 */
class Take implements TransducerInterface
{
    protected $iterator;
    protected $taken;
    protected $numberOfItems;
    public function __invoke($item)
    {
        $this->computeNextResult($item);
    }

    public function __construct(
        int $numberOfItems
    ) {
        $this->numberOfItems = $numberOfItems;
        $this->taken = [];
    }

    public function initialize(
        LazyIterator $iterator
    ) {
        $this->iterator = $iterator;
    }

    public function isPure(): bool
    {
        return false;
    }

    public function getCurrentResult()
    {
        return $this->taken;
    }

    public function getEmptyFinalResult()
    {
        return $this->taken;
    }

    public function computeNextResult($item)
    {
        if (\count($this->taken) >= $this->numberOfItems - 1) {
            $this->iterator->completeEarly();
        }
        $this->taken[] = $item;
        $this->iterator->yieldToNextTransducer($item);
    }

    public function computeFinalResult($previousResult, $lastValue)
    {
        return $this->taken;
    }
}
