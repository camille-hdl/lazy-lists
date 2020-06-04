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

use LazyLists\LazyWorker;

/**
 *
 */
class Take implements TransducerInterface
{
    protected $worker;
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
    }

    public function initialize(
        LazyWorker $worker
    ) {
        $this->taken = [];
        $this->worker = $worker;
    }

    public function getEmptyFinalResult()
    {
        return $this->taken;
    }

    public function computeNextResult($item)
    {
        if (\count($this->taken) >= $this->numberOfItems - 1) {
            $this->worker->completeEarly();
        }
        $this->taken[] = $item;
        $this->worker->yieldToNextTransducer($item);
    }

    public function computeFinalResult($previousResult, $lastValue)
    {
        return $this->taken;
    }
}
