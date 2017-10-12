<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests\Mocks;

/**
 * Mocks a serializable object
 */
class SerializableObject
{
    /** @var string The ID for the object */
    private $id = null;

    /**
     * @param string $id The ID for the object
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Gets this object as a string
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->id;
    }
}
