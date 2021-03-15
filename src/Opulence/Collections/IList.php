<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use OutOfRangeException;

/**
 * Defines the interface for lists to implement
 */
interface IList extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Adds a value
     *
     * @param mixed $value The value to add
     */
    public function add($value) : void;

    /**
     * Adds a range of values
     *
     * @param array $values The values to add
     */
    public function addRange(array $values) : void;

    /**
     * Clears all values from the list
     */
    public function clear() : void;

    /**
     * Gets whether or not the value exists
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     */
    public function containsValue($value) : bool;

    /**
     * Gets the value at an index
     *
     * @param int $index The index to get
     * @return mixed The value if it was found, otherwise the default value
     * @throws OutOfRangeException Thrown if the index is < 0 or >= than the length of the list
     */
    public function get(int $index);

    /**
     * Gets the index of a value
     *
     * @param mixed $value The value to search for
     * @return int|null The index of the value if it was found, otherwise null
     */
    public function indexOf($value) : ?int;

    /**
     * Inserts the value at an index
     *
     * @param int $index The index to insert at
     * @param mixed $value The value to insert
     */
    public function insert(int $index, $value) : void;

    /**
     * Intersects the values of the input array with the values already in the list
     *
     * @param array $values The values to intersect with
     */
    public function intersect(array $values) : void;

    /**
     * Removes the value at an index
     *
     * @param int $index The index to remove
     */
    public function removeIndex(int $index) : void;

    /**
     * Removes the value from the list
     *
     * @param mixed $value The value to remove
     */
    public function removeValue($value) : void;

    /**
     * Reverses the list
     */
    public function reverse() : void;

    /**
     * Sorts the values of the list
     *
     * @param callable $comparer The comparer to sort with
     */
    public function sort(callable $comparer) : void;

    /**
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array;

    /**
     * Unions the values of the input array with the values already in the list
     *
     * @param array $values The values to union with
     */
    public function union(array $values) : void;
}
