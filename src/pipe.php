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

function pipe(...$transducers)
{
    InvalidArgumentException::assertTransducers($transducers, __FUNCTION__);
    return function ($subject) use ($transducers) {
        $lazyIterator = new LazyIterator($subject, $transducers);
        return $lazyIterator();
    };
}
