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

namespace LazyLists\Internal;

/**
 * This class wraps a generator function (a Closure returning a Generator)
 * into an Iterator so that it can be used as a subject for LazyWorker (`pipe()`).
 *
 * @implements \Iterator<mixed, mixed>
 */
class CallableIteratorWrapper implements \Iterator
{
    /**
     * @var \Generator<mixed>|null
     */
    private ?\Generator $generator = null;
    protected \Closure $originalGeneratorFn;

    /**
     * @param \Closure(): \Generator<mixed> $generatorFn
     */
    public function __construct(\Closure $generatorFn)
    {
        $this->originalGeneratorFn = $generatorFn;
    }

    /**
     * The Generator is only created on the first call to the generator function
     */
    protected function firstCall(): void
    {
        if (!$this->hasBeenCalledOnce()) {
            $generatorFn = $this->originalGeneratorFn;
            /** @var \Generator<mixed> */
            $generatorObject = $generatorFn();
            $this->generator = $generatorObject;
        }
    }

    public function current(): mixed
    {
        $this->firstCall();
        $currentValue = $this->generator?->current() ?? null;
        return $currentValue;
    }

    public function next(): void
    {
        if (!$this->hasBeenCalledOnce()) {
            $this->firstCall();
        } else {
            $this->generator?->next();
        }
    }

    /**
     * Returns false until the Generator object has been created
     * by calling the generator function once.
     */
    protected function hasBeenCalledOnce(): bool
    {
        if ($this->generator) {
            return true;
        }
        return false;
    }

    public function key(): mixed
    {
        $currentKey = $this->generator?->key() ?? 0;
        return $currentKey;
    }

    public function valid(): bool
    {
        return $this->generator?->valid() ?? true;
    }

    /**
     * Generators cannot be rewound.
     */
    public function rewind(): void
    {
        // NO-OP
    }
}
