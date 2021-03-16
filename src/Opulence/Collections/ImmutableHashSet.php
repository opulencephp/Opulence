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
            $this->values[$this->getHashKey($value)] = $value;
        }
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
    public function toArray() : array
    {
        return array_values($this->values);
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
