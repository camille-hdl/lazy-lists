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

namespace LazyLists;

/**
 * Provides an Iterator interface to a LazyWorker
 *
 * This class can be instanciated by `LazyLists\iterate()`
 * @see \LazyLists\iterate
 * @template InputType
 * @implements \Iterator<int, mixed>
 */
class LazyIterator extends LazyWorker implements \Iterator
{
    protected int $key = 0;
    /**
     * @var array<mixed>
     */
    protected array $currentValueBuffer = [];
    protected mixed $currentValue = null;
    protected bool $hasValue = false;
    protected bool $canLoop = false;
    /**
     * @param array<InputType>|\Iterator<InputType> $subject
     * @param \LazyLists\Transducer\TransducerInterface[] $transducers
     */
    public function __construct(array|\Iterator $subject, array $transducers)
    {
        parent::__construct($subject, $transducers);
        $this->registerValueCallback(function ($value) {
            $this->currentValueBuffer[] = $value;
            $this->canLoop = false;
        });
    }

    public function iteratorInitialization(): void
    {
        $this->key = 0;
        $this->reset();
        if ($this->iterator->valid()) {
            $this->currentWorkingValue = $this->iterator->current();
            $this->finalResultSoFar = $this->getLastTransducer()?->getEmptyFinalResult();
        }
        $this->computeFirstValue();
    }

    /**
     * @return void
     */
    #[\Override]
    protected function reset()
    {
        $this->initializeTransducers();
        $this->resetTransducers();
        $this->shouldCompleteEarly = false;
        $this->completedEarly = false;
        $this->iterator->rewind();
    }

    public function rewind(): void
    {
        $this->iteratorInitialization();
    }

    /**
     * We need to read the first value in advance,
     * basically, the iterator wrapped in $this->iterator is
     * one step ahead
     *
     * @return void
     */
    protected function computeFirstValue()
    {
        if ($this->key !== 0) {
            throw new \LogicException(
                "Should not use computeFirstValue after first key"
            );
        }
        $this->loopUntilNextValue();
        $this->setCurrentValueFromBuffer();
    }

    protected function loopUntilNextValue(): void
    {
        $this->canLoop = true;
        $this->loop();
    }

    protected function setCurrentValueFromBuffer(): void
    {
        if (\count($this->currentValueBuffer) > 0) {
            $this->hasValue = true;
            $this->currentValue = \array_shift($this->currentValueBuffer);
        }
    }

    protected function computeCurrentValue(): void
    {
        $this->hasValue = false;
        $this->key++;
        if (\count($this->currentValueBuffer) <= 0) {
            $this->loopUntilNextValue();
        }
        $this->setCurrentValueFromBuffer();
    }

    public function valid(): bool
    {
        return $this->hasValue;
    }

    public function next(): void
    {
        $this->computeCurrentValue();
    }

    public function current(): mixed
    {
        return $this->currentValue;
    }

    public function key(): mixed
    {
        return $this->key;
    }

    /**
     * @return void
     */
    #[\Override]
    protected function loop()
    {
        while ($this->canLoop && $this->hasNextItem() && !$this->completedEarly) {
            $currentTransducer = $this->getCurrentTransducer();
            $currentTransducer($this->currentWorkingValue);
        }
    }
}
