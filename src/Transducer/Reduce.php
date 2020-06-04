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
class Reduce implements TransducerInterface
{
    protected $worker;
    protected $accumulator;
    protected $initialReduction;
    protected $reduction;
    public function __invoke($item)
    {
        $this->computeNextResult($item);
    }

    public function __construct(
        callable $accumulator,
        $initialReduction
    ) {
        $this->accumulator = $accumulator;
        $this->initialReduction = $initialReduction;
        $this->reduction = $initialReduction;
    }

    public function initialize(
        LazyWorker $worker
    ) {
        $this->worker = $worker;
    }

    public function getEmptyFinalResult()
    {
        return $this->initialReduction;
    }

    public function computeNextResult($item)
    {
        $accumulator = $this->accumulator;
        $this->reduction = $accumulator($this->reduction, $item);
        $this->worker->yieldToNextTransducer($this->reduction);
    }

    public function computeFinalResult($previousResult, $lastValue)
    {
        return $lastValue;
    }
}
