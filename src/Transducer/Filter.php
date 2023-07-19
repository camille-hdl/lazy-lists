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

    public function computeNextResult(mixed $item): void
    {
        $predicate = $this->predicate;
        if ($predicate($item)) {
            $this->worker?->yieldToNextTransducer($item);
        } else {
            $this->worker?->skipToNextLoop();
        }
    }

    public function getEmptyFinalResult(): mixed
    {
        return [];
    }

    public function computeFinalResult(mixed $previousResult, mixed $lastValue): mixed
    {
        if (\is_null($previousResult)) {
            return [];
        }
        if ($previousResult instanceof \ArrayAccess || \is_array($previousResult)) {
            $previousResult[] = $lastValue;
            return $previousResult;
        }
        throw new \LogicException('Cannot use Filter transducer on a non-array, non-ArrayAccess result');
    }
}
