<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayIterator;
use RuntimeException;
use Traversable;

/**
 * Defines an immutable array list
 */
class ImmutableArrayList implements IImmutableList
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values)
    {
        foreach ($values as $value) {
            $this->values[] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function containsValue($value) : bool
    {
        return $this->indexOf($value) !== null;
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
    public function get(int $index, $default = null)
    {
        return $this->values[$index] ?? $default;
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
    public function indexOf($value) : ?int
    {
        if (($index = array_search($value, $this->values)) === false) {
            return null;
        }

        return (int)$index;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($index) : bool
    {
        return array_key_exists($index, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        return $this->get($index, null);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $value) : void
    {
        throw new RuntimeException('Cannot set a value in an immutable array list');
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Cannot unset an index in an immutable array list');
    }

    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return $this->values;
    }
}
