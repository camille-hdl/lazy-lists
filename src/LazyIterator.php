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

use ArrayIterator;

use function LazyLists\map;

/**
 * lazy list processing functions
 */
class LazyIterator
{
    protected $iterator;
    protected $transducers;
    protected $currentTransducerIndex;
    protected $computedFutureValues = [];
    protected $intermediateResultSoFar;
    protected $finalResultSoFar;

    public function __construct($subject, array $transducers)
    {
        $this->iterator = self::iteratorFromSubject($subject);
        $this->transducers = $transducers;
    }

    public function __invoke()
    {
        $this->initializeTransducers();
        $this->iterator->rewind();
        if (!$this->iterator->valid()) {
            return $this->finalResultSoFar;
        }
        $this->intermediateResultSoFar = $this->iterator->current();
        $this->finalResultSoFar = $this->getLastTransducer()::getEmptyFinalResult();
        $this->loop();
        return $this->finalResultSoFar;
    }

    protected function loop()
    {
        while ($this->iterator->valid()) {
            $currentTransducer = $this->getCurrentTransducer();
            $currentTransducer($this->intermediateResultSoFar);
        }
    }

    public function readNextItem()
    {
        if (\count($this->computedFutureValues) > 0) {
            return \array_shift($this->computedFutureValues);
        }
        $this->iterator->next();
        if ($this->iterator->valid()) {
            return $this->iterator->current();
        }
    }

    protected function getCurrentTransducer()
    {
        return $this->transducers[$this->currentTransducerIndex];
    }

    protected function nextTransducer()
    {
        if (!isset($this->transducers[$this->currentTransducerIndex + 1])) {
            $this->updateFinalResult();
            $this->resetLoop();
        } else {
            $this->currentTransducerIndex++;
        }
    }

    public function yieldToNextTransducerWithFutureValues(array $futureValues)
    {
        \array_unshift($this->computedFutureValues, ...$futureValues);
        var_dump($this->computedFutureValues);
        $this->intermediateResultSoFar = $futureValues;
        $nextTransducer = $this->nextTransducer();
    }

    public function yieldToNextTransducer($newIntermediateResultSoFar)
    {
        $this->intermediateResultSoFar = $newIntermediateResultSoFar;
        $nextTransducer = $this->nextTransducer();
    }

    public function skipToNextLoop()
    {
        $this->resetLoop();
    }

    public function resetLoop()
    {
        $this->computedFutureValues = [];
        $this->currentTransducerIndex = 0;
        $this->intermediateResultSoFar = $this->readNextItem();
    }

    public function updateFinalResult()
    {
        $lastTransducer = $this->getLastTransducer();
        $this->finalResultSoFar = $lastTransducer->computeFinalResult(
            $this->finalResultSoFar,
            $this->intermediateResultSoFar
        );
    }

    protected function initializeTransducers()
    {
        $this->resetLoop();
        foreach ($this->transducers as $transducer) {
            $transducer->initialize($this);
        }
    }

    public static function iteratorFromSubject($subject)
    {
        if (\is_array($subject)) {
            return new ArrayIterator($subject);
        }
        if ($subject instanceof \Iterator) {
            return $subject;
        }
        throw new \InvalidArgumentException(
            \sprintf("Could not create Iterator from subject")
        );
    }

    protected function getLastTransducer()
    {
        if (\count($this->transducers) > 0) {
            return $this->transducers[\count($this->transducers) - 1];
        }
        return null;
    }
}
