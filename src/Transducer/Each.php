<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camilleh@hey.com>
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

    public function computeNextResult(mixed $item): void
    {
        $sideEffect = $this->sideEffect;
        $sideEffect($item);
        $this->worker?->yieldToNextTransducer($item);
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
        throw new \LogicException('Cannot use Each transducer on a non-array, non-ArrayAccess result');
    }
}
