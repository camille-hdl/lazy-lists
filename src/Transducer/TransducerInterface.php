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
interface TransducerInterface
{
    /**
     * Cette fonction DOIT se terminer par un appel sur soit
     * * $iterator->yieldToNextTransducer
     * * $iterator->yieldToNextTransducerWithFutureValues
     * * $iterator->skipToNextLoop
     *
     * @param mixed $item
     * @return void
     */
    public function __invoke($item);

    public function initialize(LazyIterator $iterator);

    public function isPure(): bool;

    public function getCurrentResult();

    public function computeNextResult($item);

    public function getEmptyFinalResult();

    public function computeFinalResult($previousResult, $lastValue);
}
