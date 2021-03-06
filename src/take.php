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
 * Returns an array with the $numberOfItems first elements from $list.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @param integer $numberOfItems
 * @param array|\Traversable|null $list
 * @see \LazyLists\Transducer\Take
 * @return mixed array or \LazyLists\Transducer\Take
 */
function take(int $numberOfItems, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Take($numberOfItems);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
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
