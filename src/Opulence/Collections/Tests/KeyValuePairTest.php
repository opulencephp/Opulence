<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Collections\Tests;

use Opulence\Collections\KeyValuePair;

/**
 * Tests the key-value pair
 */
class KeyValuePairTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingKey(): void
    {
        $kvp = new KeyValuePair('foo', 'bar');
        $this->assertEquals('foo', $kvp->getKey());
    }

    public function testGettingValue(): void
    {
        $kvp = new KeyValuePair('foo', 'bar');
        $this->assertEquals('bar', $kvp->getValue());
    }
}
