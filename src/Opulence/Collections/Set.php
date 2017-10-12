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
 * Defines a set
 */
class Set implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array The set of values */
    protected $values = [];

    /**
     * @param array $values The set of values
     */
    public function __construct(array $values = [])
    {
        $this->addRange($values);
    }

    /**
     * Adds a value
     *
     * @param mixed $value The value to add
     * @throws RuntimeException Thrown if the value cannot be serialized
     */
    public function add($value) : void
    {
        try {
            $this->values[(string)$value] = $value;
        } catch (Throwable $ex) {
            throw new RuntimeException('Could not serialize value', 0, $ex);
        }
    }

    /**
     * Adds a range of values
     *
     * @param array $values The values to add
     * @throws RuntimeException Thrown if any of the values cannot be serialized
     */
    public function addRange(array $values) : void
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * Clears all values from the set
     */
    public function clear() : void
    {
        $this->values = [];
    }

    /**
     * Gets whether or not the value exists
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     * @throws RuntimeException Thrown if the value cannot be serialized
     */
    public function containsValue($value) : bool
    {
        try {
            return isset($this->values[(string)$value]);
        } catch (Throwable $ex) {
            throw new RuntimeException('Could not serialize value', 0, $ex);
        }
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
     * Intersects the values of the input array with the values already in the set
     *
     * @param array $values The values to intersect with
     * @throws RuntimeException Thrown if any of the values cannot be serialized
     */
    public function intersect(array $values) : void
    {
        $intersectedValues = array_intersect(array_values($this->values), $values);
        $this->clear();
        $this->addRange($intersectedValues);
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
        $this->add($value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Cannot use unset on set');
    }

    /**
     * Removes a value from the set
     *
     * @param mixed $value The value to remove
     * @throws RuntimeException Thrown if the value cannot be serialized
     */
    public function removeValue($value) : void
    {
        try {
            unset($this->values[(string)$value]);
        } catch (Throwable $ex) {
            throw new RuntimeException('Could not serialize value', 0, $ex);
        }
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
     * Unions the values of the input array with the values already in the set
     *
     * @param array $values The values to union with
     * @throws RuntimeException Thrown if any of the values cannot be serialized
     */
    public function union(array $values) : void
    {
        $unionedValues = array_merge(array_values($this->values), $values);
        $this->clear();
        $this->addRange($unionedValues);
    }
}
