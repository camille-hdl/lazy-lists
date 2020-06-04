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
 * Returns an array containing the elements of `$list` up to
 * an element for which $condition returns true (excluding this element).
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @param callable $condition
 * @param array|\Traversable|null $list
 * @return mixed array or \LazyLists\Transducer\Filter
 */
function until(callable $condition, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Until($condition);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
    $output = [];
    foreach ($list as $key => $item) {
        if ($condition($item, $key)) {
            return $output;
        }
        $output[] = $item;
    }
    return $output;
}
