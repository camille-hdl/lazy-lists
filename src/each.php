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
 * Calls `$sideEffect` on each element in `$list`.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @see \LazyLists\Transducer\Map
 * @template InputType
 * @param callable(InputType, mixed|null): void $sideEffect
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Each : null)
 */
function each(callable $sideEffect, array|\Traversable|null $list = null): null|\LazyLists\Transducer\Each
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Each($sideEffect);
    }
    foreach ($list as $key => $item) {
        $sideEffect($item, $key);
    }
    return null;
}
