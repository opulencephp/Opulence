<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections\Tests;

use Opulence\Collections\KeyValuePair;

/**
 * Tests the key-value pair
 */
class KeyValuePairTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the key
     */
    public function testGettingKey() : void
    {
        $kvp = new KeyValuePair('foo', 'bar');
        $this->assertEquals('foo', $kvp->getKey());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue() : void
    {
        $kvp = new KeyValuePair('foo', 'bar');
        $this->assertEquals('bar', $kvp->getValue());
    }
}
