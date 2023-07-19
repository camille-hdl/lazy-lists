<?php

/**
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @link    https://github.com/lstrojny/functional-php/blob/master/src/Functional/Exceptions/InvalidArgumentException.php
 */

namespace LazyLists\Exception;

final class InvalidArgumentException extends \InvalidArgumentException
{
    public static function assertCollection(mixed $collection, string $callee, int $parameterPosition = 1): void
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
    private static function assertCollectionAlike(
        mixed $collection,
        string $className,
        string $callee,
        int $parameterPosition
    ): void {
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

    private static function getType(mixed $value): string
    {
        return \is_object($value) ? \get_class($value) : \gettype($value);
    }

    /**
     * @param array<mixed> $candidates
     * @param string $callee
     */
    public static function assertTransducers(array $candidates, string $callee): void
    {
        foreach ($candidates as $candidate) {
            self::assertTransducer($candidate, $callee);
        }
    }
    public static function assertTransducer(mixed $candidate, string $callee): void
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
