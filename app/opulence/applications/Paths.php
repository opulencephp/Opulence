<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the list of paths used by Opulence
 */
namespace Opulence\Applications;

use ArrayAccess;
use InvalidArgumentException;

class Paths implements ArrayAccess
{
    /** @var array The mapping of path names to values */
    private $paths = [];

    /**
     * @param array $paths The mapping of path names to values
     */
    public function __construct(array $paths)
    {
        foreach($paths as $key => $value)
        {
            $this->paths[$key] = realpath($value);
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->paths[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->paths[$offset]) ? $this->paths[$offset] : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if(is_null($offset))
        {
            throw new InvalidArgumentException("Offset cannot be empty");
        }

        $this->paths[$offset] = realpath($value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->paths[$offset]);
    }
}