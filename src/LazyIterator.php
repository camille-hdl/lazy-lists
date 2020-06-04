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
 */
class LazyIterator extends LazyWorker implements \Iterator
{
    protected $key = 0;
    protected $currentValueBuffer = [];
    protected $currentValue = null;
    protected $hasValue = false;
    protected $canLoop = false;
    /**
     * @param array|\Iterator $subject
     * @param array $transducers
     */
    public function __construct($subject, array $transducers)
    {
        parent::__construct($subject, $transducers);
        $this->registerValueCallback(function ($value) {
            $this->currentValueBuffer[] = $value;
            $this->canLoop = false;
        });
    }

    public function iteratorInitialization()
    {
        $this->key = 0;
        $this->reset();
        if ($this->iterator->valid()) {
            $this->currentWorkingValue = $this->iterator->current();
            $this->finalResultSoFar = $this->getLastTransducer()->getEmptyFinalResult();
        }
        $this->computeFirstValue();
    }

    /**
     * @return void
     */
    protected function reset()
    {
        $this->initializeTransducers();
        $this->resetTransducers();
        $this->shouldCompleteEarly = false;
        $this->completedEarly = false;
        $this->iterator->rewind();
    }

    /**
     * @return void
     */
    public function rewind()
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

    protected function loopUntilNextValue()
    {
        $this->canLoop = true;
        $this->loop();
    }

    protected function setCurrentValueFromBuffer()
    {
        if (\count($this->currentValueBuffer) > 0) {
            $this->hasValue = true;
            $this->currentValue = \array_shift($this->currentValueBuffer);
        }
    }

    protected function computeCurrentValue()
    {
        $this->hasValue = false;
        $this->key++;
        if (\count($this->currentValueBuffer) <= 0) {
            $this->loopUntilNextValue();
        }
        $this->setCurrentValueFromBuffer();
    }

    public function valid()
    {
        return $this->hasValue;
    }

    public function next()
    {
        $this->computeCurrentValue();
    }

    public function current()
    {
        return $this->currentValue;
    }

    public function key()
    {
        return $this->key;
    }

    /**
     * @return void
     */
    protected function loop()
    {
        while ($this->canLoop && $this->iterator->valid() && !$this->completedEarly) {
            $currentTransducer = $this->getCurrentTransducer();
            $currentTransducer($this->currentWorkingValue);
        }
    }
}
