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

/**
 * @see \LazyLists\filter
 */
class Until extends PureTransducer implements TransducerInterface
{
    /**
     * Skips the $item if `$predicat($item)` returns falsy
     *
     * @var callable
     */
    protected $condition;

    public function __construct(callable $condition)
    {
        $this->condition = $condition;
    }

    public function computeNextResult($item)
    {
        $condition = $this->condition;
        if ($condition($item)) {
            $this->iterator->completeEarly();
            $this->iterator->skipToNextLoop();
        } else {
            $this->iterator->yieldToNextTransducer($item);
        }
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
