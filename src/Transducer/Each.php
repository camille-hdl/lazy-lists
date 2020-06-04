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

class Each extends PureTransducer implements TransducerInterface
{
    /**
     * Applies $sideEffect to each $item, but does not change the $item itself
     *
     * @var callable
     */
    protected $sideEffect;

    public function __construct(callable $sideEffect)
    {
        $this->sideEffect = $sideEffect;
    }

    public function computeNextResult($item)
    {
        $sideEffect = $this->sideEffect;
        $sideEffect($item);
        $this->iterator->yieldToNextTransducer($item);
    }

    public function getEmptyFinalResult()
    {
        return [];
    }

    public function computeFinalResult($previousResult, $lastValue)
    {
        if (\is_null($previousResult)) {
            return [];
        }
        $previousResult[] = $lastValue;
        return $previousResult;
    }
}
