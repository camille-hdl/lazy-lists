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
use function LazyLists\filter;

/**
 * lazy list processing functions
 */
class LazyIterator
{
    protected $iterator;
    protected $transducers;
    protected $currentTransducerIndex;
    protected $computedFutureValues = [];
    protected $currentWorkingValue;
    protected $finalResultSoFar;
    protected $shouldCompleteEarly = false;
    protected $completedEarly = false;

    public function __construct($subject, array $transducers)
    {
        $this->iterator = self::iteratorFromSubject($subject);
        $this->transducers = $transducers;
    }

    public function __invoke()
    {
        $this->initializeTransducers();
        $this->shouldCompleteEarly = false;
        $this->completedEarly = false;
        $this->iterator->rewind();
        if (!$this->iterator->valid()) {
            return $this->finalResultSoFar;
        }
        $this->currentWorkingValue = $this->iterator->current();
        $this->finalResultSoFar = $this->getLastTransducer()->getEmptyFinalResult();
        $this->loop();
        return $this->finalResultSoFar;
    }

    protected function loop()
    {
        while ($this->iterator->valid() && !$this->completedEarly) {
            $currentTransducer = $this->getCurrentTransducer();
            $currentTransducer($this->currentWorkingValue);
        }
    }

    public function readNextItem()
    {
        $computedValuesInfo = $this->computedValuesToProcess();
        if (!\is_null($computedValuesInfo)) {
            return $this->readComputedValueToProcessForIndex(
                $computedValuesInfo["index"]
            );
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
            if ($this->shouldCompleteEarly) {
                $this->completedEarly = true;
            }
            $this->updateFinalResult();
            $this->resetLoop();
        } else {
            $this->currentTransducerIndex++;
        }
    }

    public function yieldToNextTransducerWithFutureValues(array $futureValues)
    {
        $index = $this->currentTransducerIndex;
        $nextWorkingValue = \array_shift($futureValues);
        $this->computedFutureValues[$index + 1] = $futureValues;
        $this->currentWorkingValue = $nextWorkingValue;
        $nextTransducer = $this->nextTransducer();
    }

    public function yieldToNextTransducer($newCurrentWorkingValue)
    {
        $this->currentWorkingValue = $newCurrentWorkingValue;
        $nextTransducer = $this->nextTransducer();
    }

    public function skipToNextLoop()
    {
        $this->resetLoop();
    }

    public function completeEarly()
    {
        $this->shouldCompleteEarly = true;
    }

    protected function hasComputedValuesForIndex(int $index)
    {
        return isset($this->computedFutureValues[$index]) &&
            \count($this->computedFutureValues[$index]) > 0;
    }

    protected function readComputedValueToProcessForIndex(int $index)
    {
        if (
            isset($this->computedFutureValues[$index]) &&
            \count($this->computedFutureValues[$index]) > 0
        ) {
            return \array_shift($this->computedFutureValues[$index]);
        }
        return null;
    }

    protected function computedValuesToProcess()
    {
        for ($i = $this->currentTransducerIndex; $i > 0; $i--) {
            if (
                $this->hasComputedValuesForIndex($i)
            ) {
                return [
                    "index" => $i,
                    "values" => $this->computedFutureValues[$i]
                ];
            }
        }
        return null;
    }

    public function resetLoop()
    {
        $computedValuesToProcess = $this->computedValuesToProcess();
        if (\is_null($computedValuesToProcess)) {
            $this->computedFutureValues = [];
            $this->currentTransducerIndex = 0;
        } else {
            $this->currentTransducerIndex = $computedValuesToProcess["index"];
        }
        $this->currentWorkingValue = $this->readNextItem();
    }

    protected function readAllFutureComputedValues(int $fromIndex): array
    {
        $output = [];
        $isFutureTransducerIndexWithComputedValues = function ($index) use ($fromIndex) {
            return $index > $fromIndex && $this->hasComputedValuesForIndex($index);
        };
        $getAllFutureValuesForIndex = function ($index) {
            $output = [];
            while ($this->hasComputedValuesForIndex($index)) {
                $output[] = $this->readComputedValueToProcessForIndex($index);
            }
            return $output;
        };
        $indices = filter(
            $isFutureTransducerIndexWithComputedValues,
            \array_keys($this->computedFutureValues)
        );
        $futureValuesPerIndex = map(
            $getAllFutureValuesForIndex,
            $indices
        );
        return flatten(1, $futureValuesPerIndex);
    }

    public function updateFinalResult()
    {
        $lastTransducer = $this->getLastTransducer();
        $this->finalResultSoFar = $lastTransducer->computeFinalResult(
            $this->finalResultSoFar,
            $this->currentWorkingValue
        );
        /**
         * If we have "orphans" computedFutureValues,
         * we have to add them to the final result
         */
        foreach (
            $this->readAllFutureComputedValues($this->currentTransducerIndex) as $value
        ) {
            $this->finalResultSoFar = $lastTransducer->computeFinalResult(
                $this->finalResultSoFar,
                $value
            );
        }
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
