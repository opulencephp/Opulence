<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;
use Throwable;
use Traversable;

/**
 * Defines an immutable hash set
 */
class ImmutableHashSet implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array The set of values */
    protected $values = [];

    /**
     * @param array $values The set of values
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function __construct(array $values)
    {
        foreach ($values as $value) {
            $this->values[$this->getKey($value)] = $value;
        }
    }

    /**
     * Gets whether or not the value exists
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsValue($value) : bool
    {
        return isset($this->values[$this->getKey($value)]);
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($index) : bool
    {
        throw new RuntimeException('Cannot use isset on set - use containsValue() instead');
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        throw new RuntimeException('Cannot get a value from a set');
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $value) : void
    {
        throw new RuntimeException('Cannot add values to an immutable sets');
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Cannot use unset on immutable sets');
    }

    /**
     * Sorts the values of the set
     *
     * @param callable $comparer The comparer to sort with
     */
    public function sort(callable $comparer) : void
    {
        usort($this->values, $comparer);
    }

    /**
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array
    {
        return array_values($this->values);
    }

    /**
     * Gets the key for a value to use in the set
     *
     * @param mixed $value The value whose key we want
     * @return string The key for the value
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    protected function getKey($value) : string
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        }

        if (is_array($value)) {
            return md5(serialize($value));
        }

        if (is_resource($value)) {
            return "$value";
        }

        try {
            return (string)$value;
        } catch (Throwable $ex) {
            throw new RuntimeException('Value could not be converted to a key', 0, $ex);
        }
    }
}
