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
 * Calls `$sideEffect` on each element in `$list`.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @param callable $sideEffect
 * @param array|\Traversable|null $list
 * @see \LazyLists\Transducer\Map
 * @return mixed void or \LazyLists\Transducer\Map
 */
function each(callable $sideEffect, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Each($sideEffect);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
    foreach ($list as $key => $item) {
        $sideEffect($item, $key);
    }
}
