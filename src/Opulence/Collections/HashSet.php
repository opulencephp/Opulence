<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
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
        $this->values[$this->getHashKey($value)] = $value;
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
        return isset($this->values[$this->getHashKey($value)]);
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
        foreach ($this->values as $value) {
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
    public function removeValue($value) : void
    {
        unset($this->values[$this->getHashKey($value)]);
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

    /**
     * Gets the hash key for a value
     * This method allows extending classes to customize how hash keys are calculated
     *
     * @param string|int|float|array|object|resource $value The value whose hash key we want
     * @return string The hash key
     * @throws RuntimeException Thrown if the hash key could not be calculated
     */
    protected function getHashKey($value) : string
    {
        return $this->keyHasher->getHashKey($value);
    }
}
