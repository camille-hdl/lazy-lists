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

use LazyLists\Exception\InvalidArgumentException;

/**
 * Accumulates each element of $list into a single value.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @param callable $accumulator
 * @param mixed $initialReduction
 * @param array|\Traversable|null $list
 * @see \LazyLists\Transducer\Reduce
 * @return mixed
 */
function reduce(callable $accumulator, $initialReduction, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Reduce($accumulator, $initialReduction);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 3);
    $reduction = $initialReduction;
    foreach ($list as $key => $item) {
        $reduction = $accumulator($reduction, $item, $key);
    }
    return $reduction;
}
