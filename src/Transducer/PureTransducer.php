<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camilleh@hey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace LazyLists\Transducer;

use LazyLists\LazyWorker;

/**
 * Models a simple transducer that maintains no internal state.
 * For example : Map
 */
abstract class PureTransducer
{
    protected ?LazyWorker $worker = null;
    public function __invoke(mixed $item): void
    {
        $this->computeNextResult($item);
    }

    public function initialize(LazyWorker $worker): void
    {
        $this->worker = $worker;
    }

    abstract public function computeNextResult(mixed $item): void;
}
