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
 * Returns an array with the $numberOfItems first elements from $list.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @see \LazyLists\Transducer\Take
 * @template InputType
 * @param integer $numberOfItems
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Take : array<InputType>)
 */
function take(int $numberOfItems, array|\Traversable|null $list = null): array|\LazyLists\Transducer\Take
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Take($numberOfItems);
    }
    $output = [];
    foreach ($list as $item) {
        if (\count($output) < $numberOfItems) {
            $output[] = $item;
        } else {
            return $output;
        }
    }
    return $output;
}
