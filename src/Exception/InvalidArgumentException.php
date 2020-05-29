<?php

/**
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @link    https://github.com/lstrojny/functional-php/blob/master/src/Functional/Exceptions/InvalidArgumentException.php
 */

namespace LazyLists\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function assertCollection($collection, $callee, $parameterPosition)
    {
        self::assertCollectionAlike($collection, 'Traversable', $callee, $parameterPosition);
    }

    /**
     * @param  mixed   $collection        the candidate
     * @param  string  $className
     * @param  string  $callee
     * @param  integer $parameterPosition
     * @throws InvalidArgumentException
     */
    private static function assertCollectionAlike($collection, $className, $callee, $parameterPosition)
    {
        if (!\is_array($collection) && !$collection instanceof $className) {
            throw new static(
                \sprintf(
                    '%s() expects parameter %d to be array or instance of %s, %s given',
                    $callee,
                    $parameterPosition,
                    $className,
                    self::getType($collection)
                )
            );
        }
    }

    /**
     * @param  mixed $value
     * @return string
     */
    private static function getType($value): string
    {
        return \is_object($value) ? \get_class($value) : \gettype($value);
    }

    public static function assertTransducers(array $candidates, $callee)
    {
        foreach ($candidates as $candidate) {
            self::assertTransducer($candidate, $callee);
        }
    }
    public static function assertTransducer($candidate, $callee)
    {
        if (!$candidate instanceof \LazyLists\Transducer\TransducerInterface) {
            throw new static(
                \sprintf(
                    '%s() expects LazyLists\Transducer\TransducerInterface',
                    $callee
                )
            );
        }
    }
}
