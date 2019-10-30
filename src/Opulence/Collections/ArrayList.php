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
use OutOfRangeException;
use Traversable;

/**
 * Defines an array list
 */
class ArrayList implements IList
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values = [])
    {
        $this->addRange($values);
    }

    /**
     * @inheritdoc
     */
    public function add($value) : void
    {
        $this->values[] = $value;
    }

    /**
     * @inheritdoc
     */
    public function addRange(array $values) : void
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * @inheritdoc
     */
    public function clear() : void
    {
        $this->values = [];
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
    public function get(int $index)
    {
        if ($index < 0 || $index >= count($this)) {
            throw new OutOfRangeException("Index $index is out of range");
        }

        return $this->values[$index];
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
    public function insert(int $index, $value) : void
    {
        array_splice($this->values, $index, 0, $value);
    }

    /**
     * @inheritdoc
     */
    public function intersect(array $values) : void
    {
        $intersectedValues = array_intersect($this->values, $values);
        $this->clear();
        $this->addRange($intersectedValues);
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
        return $this->get($index);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $value) : void
    {
        $this->insert($index, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        $this->removeIndex($index);
    }

    /**
     * @inheritdoc
     */
    public function removeIndex(int $index) : void
    {
        unset($this->values[$index]);
    }

    /**
     * @inheritdoc
     */
    public function removeValue($value) : void
    {
        $index = $this->indexOf($value);

        if ($index !== null) {
            $this->removeIndex($index);
        }
    }

    /**
     * @inheritdoc
     */
    public function reverse() : void
    {
        $this->values = array_reverse($this->values);
    }

    /**
     * @inheritdoc
     */
    public function sort(callable $comparer) : void
    {
        usort($this->values, $comparer);
    }

    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function union(array $values) : void
    {
        $unionedValues = array_merge(($this->values), $values);
        $this->clear();
        $this->addRange($unionedValues);
    }
}
