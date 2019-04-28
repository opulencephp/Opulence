<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections\Tests\Mocks;

/**
 * Mocks a serializable object
 */
class SerializableObject
{
    /** @var string The string value to serialize to */
    private $stringValue = '';

    /**
     * @param string $stringValue The string value to serialize to
     */
    public function __construct(string $stringValue)
    {
        $this->stringValue = $stringValue;
    }

    /**
     * @inheritdoc
     */
    public function __toString() : string
    {
        return $this->stringValue;
    }
}
