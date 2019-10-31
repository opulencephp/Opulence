<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections;

use Countable;
use IteratorAggregate;
use RuntimeException;

/**
 * Defines the interface for immutable sets to implement
 */
interface IImmutableSet extends Countable, IteratorAggregate
{
    /**
     * Gets whether or not the value exists
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsValue($value) : bool;

    /**
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array;
}
