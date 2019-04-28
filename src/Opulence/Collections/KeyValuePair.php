<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Collections;

/**
 * Defines a key-value pair
 */
class KeyValuePair
{
    /** @var mixed The key */
    private $key;
    /** @var mixed The value */
    private $value;

    /**
     * @param mixed $key The key
     * @param mixed $value The value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Gets the key
     *
     * @return mixed The key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Gets the value
     *
     * @return mixed The value
     */
    public function getValue()
    {
        return $this->value;
    }
}
