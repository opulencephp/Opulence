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
 * Defines a hash set
 */
class HashSet implements ISet
{
    /** @var array The set of values */
    protected $values = [];
    /** @var KeyHasher The key hasher to use */
    private $keyHasher = null;

    /**
     * @param array $values The set of values
     */
    public function __construct(array $values = [])
    {
        $this->keyHasher = new KeyHasher();
        $this->addRange($values);
    }

    /**
     * @inheritdoc
     */
    public function add($value) : void
    {
        $this->values[$this->keyHasher->getHashKey($value)] = $value;
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
        return isset($this->values[$this->keyHasher->getHashKey($value)]);
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
        return new ArrayIterator(array_values($this->values));
    }

    /**
     * @inheritdoc
     */
    public function intersect(array $values) : void
    {
        $intersectedValues = [];

        // We don't use array_intersect because that does string comparisons, which requires __toString()
        foreach ($this->values as $key => $value) {
            if (in_array($value, $values, true)) {
                $intersectedValues[] = $value;
            }
        }

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
     * @throws RuntimeException Thrown if the value's key could not be calculated
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
     * @inheritdoc
     */
    public function removeValue($value) : void
    {
        unset($this->values[$this->keyHasher->getHashKey($value)]);
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
        return array_values($this->values);
    }

    /**
     * @inheritdoc
     */
    public function union(array $values) : void
    {
        $unionedValues = array_merge(array_values($this->values), $values);
        $this->clear();
        $this->addRange($unionedValues);
    }
}
