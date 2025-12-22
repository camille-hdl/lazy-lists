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

/**
 *
 */
class Map extends PureTransducer implements TransducerInterface
{
    /**
     * Maps an item of the input space to an item of the output space
     * output = $procedure(input);
     *
     * @var callable
     */
    protected $procedure;

    public function __construct(callable $procedure)
    {
        $this->procedure = $procedure;
    }

    public function computeNextResult(mixed $item): void
    {
        $procedure = $this->procedure;
        $this->worker?->yieldToNextTransducer($procedure($item));
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
        throw new \LogicException('Cannot use Map transducer on a non-array, non-ArrayAccess result');
    }
}
