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

use function LazyLists\isAssociativeArray;

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

    /**
     * @param mixed $subject
     * @return boolean
     * @phpstan-assert array<mixed> $subject
     */
    protected static function isPlainArray($subject): bool
    {
        return \is_array($subject) && !isAssociativeArray($subject);
    }

    /**
     * @param integer $levels
     * @param array<mixed>|\Traversable<mixed> $item
     * @return array<mixed>
     */
    public static function flattenItem(int $levels, array|\Traversable $item): array
    {
        $output = [];
        foreach ($item as $child) {
            if (((\is_array($child) && self::isPlainArray($child)) || $child instanceof \Traversable) && $levels > 1) {
                $output = \array_merge($output, self::flattenItem($levels - 1, $child));
            } else {
                $output[] = $child;
            }
        }
        return $output;
    }

    public function computeNextResult(mixed $item): void
    {
        if (!\is_array($item)) {
            $this->worker?->yieldToNextTransducer($item);
            return;
        }
        if (isAssociativeArray($item)) {
            $this->worker?->yieldToNextTransducer($item);
            return;
        }
        $flattened = self::flattenItem($this->levels, $item);
        $this->worker?->yieldToNextTransducerWithFutureValues($flattened);
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
        throw new \LogicException('Cannot use Flatten transducer on a non-array, non-ArrayAccess result');
    }
}
