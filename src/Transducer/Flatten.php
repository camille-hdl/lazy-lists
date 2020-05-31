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

use function LazyLists\isAssociativeArray;

/**
 *
 */
class Flatten extends PureTransducer implements TransducerInterface
{
    /**
     * Flattens nested array $levels deep
     *
     * @var int
     */
    protected $levels;

    public function __construct(int $levels)
    {
        $this->levels = $levels;
    }

    public static function flattenItem($levels, $item)
    {
        $output = [];
        foreach ($item as $child) {
            if (\is_array($child) && $levels > 1) {
                $output = array_merge($output, self::flattenItem($levels - 1, $child));
            } else {
                $output[] = $child;
            }
        }
        return $output;
    }

    public function computeNextResult($item)
    {
        if (!\is_array($item)) {
            $this->iterator->yieldToNextTransducer($item);
            return;
        }
        if (isAssociativeArray($item)) {
            $this->iterator->yieldToNextTransducer($item);
            return;
        }
        $flattened = self::flattenItem($this->levels, $item);
        $this->iterator->yieldToNextTransducerWithFutureValues($flattened);
    }

    public static function getEmptyFinalResult()
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
