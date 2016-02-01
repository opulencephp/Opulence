<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Defines the list of paths used by the application
 */
class Paths implements ArrayAccess
{
    /** @var array The mapping of path names to values */
    private $paths = [];

    /**
     * @param array $paths The mapping of path names to values
     */
    public function __construct(array $paths)
    {
        foreach ($paths as $key => $value) {
            $this->paths[$key] = realpath($value);
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset) : bool
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
        if ($offset === null) {
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