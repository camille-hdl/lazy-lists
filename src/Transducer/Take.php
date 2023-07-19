<?php

/**
 * This file is part of the camille-hdl/lazy-lists library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Camille Hodoul <camille.hodoul@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace LazyLists\Transducer;

use LazyLists\LazyWorker;

/**
 *
 */
class Take implements TransducerInterface
{
    protected ?LazyWorker $worker = null;
    /**
     * @var array<mixed>
     */
    protected array $taken = [];
    protected int $numberOfItems;
    public function __invoke($item)
    {
        $this->computeNextResult($item);
    }

    public function __construct(
        int $numberOfItems
    ) {
        $this->numberOfItems = $numberOfItems;
    }

    public function initialize(
        LazyWorker $worker
    ): void {
        $this->taken = [];
        $this->worker = $worker;
    }

    public function getEmptyFinalResult(): mixed
    {
        return $this->taken;
    }

    public function computeNextResult(mixed $item): void
    {
        if (\count($this->taken) >= $this->numberOfItems - 1) {
            $this->worker?->completeEarly();
        }
        $this->taken[] = $item;
        $this->worker?->yieldToNextTransducer($item);
    }

    public function computeFinalResult(mixed $previousResult, mixed $lastValue): mixed
    {
        return $this->taken;
    }
}
