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
 * Combines Transducers to create a single "pipeline" function.
 * ```php
 * $pipeline = pipe(filter($fn1), map($fn2), flatten($fn3));
 * $result = $pipeline($iterator);
 * ```
 *
 * @param \LazyLists\Transducer\TransducerInterface[] ...$transducers
 * @throws \LazyLists\Exception\InvalidArgumentException
 * @return callable
 */
function pipe(\LazyLists\Transducer\TransducerInterface ...$transducers)
{
    InvalidArgumentException::assertTransducers($transducers, __FUNCTION__);
    return function ($subject) use ($transducers) {
        $LazyWorker = new LazyWorker($subject, $transducers);
        return $LazyWorker();
    };
}
