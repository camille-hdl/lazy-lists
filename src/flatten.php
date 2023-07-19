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
 * Flattens nested arrays $levels deep. Ignores associative arrays.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @see \LazyLists\Transducer\Flatten
 * @template InputType
 * @param integer $levels
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Flatten : array<InputType>|\Traversable<InputType>)
 */
function flatten(int $levels, array|\Traversable|null $list = null): array|\LazyLists\Transducer\Flatten|\Traversable
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Flatten($levels);
    }
    if ($levels <= 0 || !\is_array($list)) {
        return $list;
    }
    $output = [];
    $flattenItem = static function ($item) use (&$output, $levels) {
        foreach ($item as $child) {
            if (\is_array($child) && $levels > 1) {
                $flattenedChildren = flatten($levels - 1, $child);
                if (\is_array($flattenedChildren)) {
                    $output = array_merge($output, $flattenedChildren);
                }
            } else {
                $output[] = $child;
            }
        }
    };
    foreach ($list as $item) {
        if (\is_array($item) && !isAssociativeArray($item)) {
            $flattenItem($item);
        } else {
            $output[] = $item;
        }
    }
    return $output;
}
