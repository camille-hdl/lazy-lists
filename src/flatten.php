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

function flatten(int $levels, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Flatten($levels);
    }
    if ($levels <= 0 || !\is_array($list)) {
        return $list;
    }
    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
    $output = [];
    $flattenItem = static function ($item) use (&$output, $levels) {
        foreach ($item as $child) {
            if (\is_array($child) && $levels > 1) {
                $output = array_merge($output, flatten($levels - 1, $child));
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
