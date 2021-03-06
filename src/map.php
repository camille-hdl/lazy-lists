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
 * Applies `$procedure` to each element in `$list` and returns an array of the result.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @param callable $procedure
 * @param array|\Traversable|null $list
 * @see \LazyLists\Transducer\Map
 * @return mixed array or \LazyLists\Transducer\Map
 */
function map(callable $procedure, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Map($procedure);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
    $output = [];
    foreach ($list as $key => $item) {
        $output[] = $procedure($item, $key);
    }
    return $output;
}
