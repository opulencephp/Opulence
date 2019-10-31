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
use Countable;
use IteratorAggregate;
use OutOfRangeException;

/**
 * Defines the interface for immutable lists to implement
 */
interface IImmutableList extends ArrayAccess, Countable, IteratorAggregate
{
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
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array;
}
