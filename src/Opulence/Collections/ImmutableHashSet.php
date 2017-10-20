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
 * Defines an immutable hash set
 */
class ImmutableHashSet implements IImmutableSet
{
    /** @var array The set of values */
    protected $values = [];
    /** @var KeyHasher The key hasher to use */
    private $keyHasher = null;

    /**
     * @param array $values The set of values
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function __construct(array $values)
    {
        $this->keyHasher = new KeyHasher();

        foreach ($values as $value) {
            $this->values[$this->keyHasher->getHashKey($value)] = $value;
        }
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
    public function offsetExists($index) : bool
    {
        throw new RuntimeException('Cannot use isset on set - use containsValue() instead');
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        throw new RuntimeException('Cannot get a value in ' . self::class);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $value) : void
    {
        throw new RuntimeException('Cannot set values in ' . self::class);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Cannot unset values in ' . self::class);
    }

    /**
     * @inheritdoc
     */
    public function toArray() : array
    {
        return array_values($this->values);
    }
}
