<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camille.hodoul@gmail.com>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace LazyLists;

/**
 * Accumulates each element of $list into a single value.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @see \LazyLists\Transducer\Reduce
 * @template ReductionType
 * @template InputType
 * @param callable(ReductionType $reduction, InputType $item, mixed $key): ReductionType $accumulator
 * @param ReductionType $initialReduction
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Reduce : ReductionType)
 */
function reduce(callable $accumulator, mixed $initialReduction, array|\Traversable|null $list = null): mixed
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Reduce($accumulator, $initialReduction);
    }
    $reduction = $initialReduction;
    foreach ($list as $key => $item) {
        $reduction = $accumulator($reduction, $item, $key);
    }
    return $reduction;
}
