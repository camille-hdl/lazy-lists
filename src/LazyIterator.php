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
 * Wraps an Iterator and orchestrates the Transducers
 * Transducers can iteract with the LazyIterator through its
 * public methods.
 *
 * This class can be instanciated by `LazyLists\pipe()`
 * @see \LazyLists\pipe
 */
class LazyIterator
{
    /**
     * @var \Iterator
     */
    protected $iterator;
    /**
     * @var \LazyLists\Transducer\TransducerInterface[]
     */
    protected $transducers;
    /**
     * @var int
     */
    protected $currentTransducerIndex;
    /**
     * @var mixed[]
     */
    protected $computedFutureValues = [];
    /**
     * @var mixed
     */
    protected $currentWorkingValue;
    /**
     * @var mixed
     */
    protected $finalResultSoFar;
    /**
     * If true, iteration will stop once `resetLoop` has been called,
     *
     * @var boolean
     */
    protected $shouldCompleteEarly = false;
    /**
     * If true, will stop iteration in `loop`
     *
     * @var boolean
     */
    protected $completedEarly = false;

    /**
     * @param array|\Iterator $subject
     * @param array $transducers
     */
    public function __construct($subject, array $transducers)
    {
        if (\count($transducers) <= 0) {
            throw new \LogicException(
                "No transducers were provided"
            );
        }
        $this->iterator = self::iteratorFromSubject($subject);
        $this->transducers = $transducers;
    }

    /**
     * @return mixed
     */
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

    /**
     * @return void
     */
    protected function loop()
    {
        while ($this->iterator->valid() && !$this->completedEarly) {
            $currentTransducer = $this->getCurrentTransducer();
            $currentTransducer($this->currentWorkingValue);
        }
    }

    /**
     * During iteration, provides the next item to be
     * processed, either from the wrapped Iterator or
     * from a value provided by an earlier transducer
     *
     * @return mixed
     */
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

    /**
     * Called from the Transducers.
     * Lets a Transducer provide the value to be processed by the next Transducer
     *
     * @see \LazyLists\Transducer\Map
     * @param mixed $newCurrentWorkingValue
     * @return void
     */
    public function yieldToNextTransducer($newCurrentWorkingValue)
    {
        $this->currentWorkingValue = $newCurrentWorkingValue;
        $this->nextTransducer();
    }

    /**
     * If a Transducer needs to provide multiple values to be processed
     * by the Transducers downstream, it can call this method.
     * For example : `Flatten`
     *
     * @param array $futureValues
     * @see \LazyLists\Transducer\Flatten
     * @return void
     */
    public function yieldToNextTransducerWithFutureValues(array $futureValues)
    {
        $index = $this->currentTransducerIndex;
        $nextWorkingValue = \array_shift($futureValues);
        $this->computedFutureValues[$index + 1] = $futureValues;
        $this->currentWorkingValue = $nextWorkingValue;
        $this->nextTransducer();
    }

    /**
     * Can be called to prevent the value from being processed
     * by the Transducers downstream and go to the next iteration.
     * For example : `Filter`
     *
     * @see \LazyLists\Transducer\Filter
     * @return void
     */
    public function skipToNextLoop()
    {
        $this->resetLoop();
    }

    /**
     * A Transducer can call this if iteration should be
     * stopped after the current loop through the Transducers
     * has finished.
     *
     * @return void
     */
    public function completeEarly()
    {
        $this->shouldCompleteEarly = true;
    }

    /**
     * Returns the Transducer currently being applied
     * during the iteration
     *
     * @return \LazyLists\Transducer\TransducerInterface
     */
    protected function getCurrentTransducer()
    {
        return $this->transducers[$this->currentTransducerIndex];
    }

    /**
     * Moves forward in the transducer list or
     * resets the loop
     *
     * @return void
     */
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

    /**
     * Computed values are values provided by transducers,
     * for example `Flatten`.
     * They are available only to the transducers downstream
     * from the transducer that provided them.
     *
     * @param integer $index
     * @return boolean
     */
    protected function hasComputedValuesForIndex(int $index)
    {
        return isset($this->computedFutureValues[$index]) &&
            \count($this->computedFutureValues[$index]) > 0;
    }

    /**
     * @param integer $index
     * @see hasComputedValuesForIndex
     * @return mixed
     */
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

    /**
     * Checks if there are computed values provided from
     * transducers upstream
     *
     * @return mixed
     */
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

    /**
     * Starts a new loop in the transducers,
     * and reads the next value to be processed.
     *
     * If the value is provided from a transducers (as opposed to
     * the wrapped iterator), we don't start the loop from the
     * beginning but only from the Transducers downstream from the one
     * who provided the value.
     *
     * @return void
     */
    protected function resetLoop()
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

    /**
     * Undocumented function
     *
     * @param integer $fromIndex
     * @return array
     */
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

    /**
     * Prepare the final result before returning
     *
     * @return void
     */
    protected function updateFinalResult()
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

    /**
     * @return void
     */
    protected function initializeTransducers()
    {
        $this->resetLoop();
        foreach ($this->transducers as $transducer) {
            $transducer->initialize($this);
        }
    }

    /**
     * @param  array|\Iterator $subject
     * @return \Iterator
     */
    protected static function iteratorFromSubject($subject)
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

    /**
     * @return \LazyLists\Transducer\TransducerInterface
     */
    protected function getLastTransducer()
    {
        return $this->transducers[\count($this->transducers) - 1];
    }
}
