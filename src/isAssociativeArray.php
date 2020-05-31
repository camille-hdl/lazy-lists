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

function isAssociativeArray($subject): bool
{
    if (!\is_array($subject)) {
        return false;
    }
    $keys = \array_keys($subject);
    foreach ($keys as $key) {
        if (\is_string($key)) {
            return true;
        }
    }
    return false;
}
