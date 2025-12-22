<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camilleh@hey.com>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace LazyLists;

/**
 * Returns an array containing the elements of `$list` up to
 * an element for which $condition returns true (including this element).
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @template InputType
 * @param callable $condition
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Until : array<InputType>)
 */
function until(callable $condition, \Traversable|array|null $list = null): array|\LazyLists\Transducer\Until
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Until($condition);
    }
    $output = [];
    foreach ($list as $key => $item) {
        if ($condition($item, $key)) {
            return $output;
        }
        $output[] = $item;
    }
    return $output;
}
