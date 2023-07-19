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

/**
 * Combines Transducers to create a single "pipeline" function.
 * ```php
 * $pipeline = pipe(filter($fn1), map($fn2), flatten($fn3));
 * $result = $pipeline($iterator);
 * ```
 *
 * @param \LazyLists\Transducer\TransducerInterface[] ...$transducers
 * @return callable
 */
function iterate(\LazyLists\Transducer\TransducerInterface ...$transducers)
{
    return function ($subject) use ($transducers) {
        return new LazyIterator($subject, $transducers);
    };
}
