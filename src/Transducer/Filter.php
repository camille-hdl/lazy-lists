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
 *
 */
class Filter extends PureTransducer implements TransducerInterface
{
    /**
     * Skips the $item if `$predicat($item)` returns falsy
     *
     * @var callable
     */
    protected $predicate;

    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    public function computeNextResult($item)
    {
        $predicate = $this->predicate;
        if ($predicate($item)) {
            $this->iterator->yieldToNextTransducer($item);
        } else {
            $this->iterator->skipToNextLoop();
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
