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
 * Returns an array containing the elements of `$list` for which
 * `$predicate` returns truthy.
 * If $list is omitted, returns a Transducer to be used with `pipe()` instead.
 *
 * @template InputType
 * @param callable(InputType, mixed|null): bool $predicate
 * @param array<InputType>|\Traversable<InputType>|null $list
 * @return ($list is null ? \LazyLists\Transducer\Filter : array<InputType>)
 */
function filter(callable $predicate, array|\Traversable|null $list = null): array|\LazyLists\Transducer\Filter
{
    if (\is_null($list)) {
        return new \LazyLists\Transducer\Filter($predicate);
    }
    $output = [];
    foreach ($list as $key => $item) {
        if ($predicate($item, $key)) {
            $output[] = $item;
        }
    }
    return $output;
}
