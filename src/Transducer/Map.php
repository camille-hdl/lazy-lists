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

    public function computeNextResult($item)
    {
        $procedure = $this->procedure;
        $this->iterator->yieldToNextTransducer($procedure($item));
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
