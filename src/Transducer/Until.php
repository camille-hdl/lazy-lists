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

use ArrayAccess;

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

    public function computeNextResult(mixed $item): void
    {
        $condition = $this->condition;
        if ($condition($item)) {
            $this->worker?->completeEarly();
            $this->worker?->skipToNextLoop();
        } else {
            $this->worker?->yieldToNextTransducer($item);
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
        throw new \LogicException('Cannot use Until transducer on a non-array, non-ArrayAccess result');
    }
}
