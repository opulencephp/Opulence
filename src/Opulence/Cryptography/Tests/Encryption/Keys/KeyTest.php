<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cryptography\Tests\Encryption\Keys;

use Opulence\Cryptography\Encryption\Keys\Key;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;

/**
 * Tests the key
 */
class KeyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests setting a valid key
     */
    public function testValidKey()
    {
        $value = str_repeat('a', 32);
        $key = new Key($value);
        $this->assertEquals(SecretTypes::KEY, $key->getType());
        $this->assertEquals($value, $key->getValue());
    }
}
