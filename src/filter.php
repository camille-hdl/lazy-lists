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

function filter(callable $predicate, $list = null)
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Filter($predicate);
    }

    InvalidArgumentException::assertCollection($list, __FUNCTION__, 2);
    $output = [];
    foreach ($list as $key => $item) {
        if ($predicate($item, $key)) {
            $output[] = $item;
        }
    }
    return $output;
}
