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

interface TransducerInterface
{
    /**
     * This function should make use of:
     * * $worker->yieldToNextTransducer
     * * $worker->yieldToNextTransducerWithFutureValues
     * * $worker->skipToNextLoop
     * * $worker->completeEarly
     *
     * @param mixed $item
     * @return void
     */
    public function __invoke(mixed $item);

    /**
     * @param LazyWorker $worker
     * @return void
     */
    public function initialize(LazyWorker $worker): void;

    public function computeNextResult(mixed $item): void;

    public function getEmptyFinalResult(): mixed;

    public function computeFinalResult(mixed $previousResult, mixed $lastValue): mixed;
}
