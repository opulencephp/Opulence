<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Cryptography\Tests\Encryption\Keys;

use Opulence\Cryptography\Encryption\Keys\Password;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;

/**
 * Tests the key
 */
class PasswordTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $key = new Password('foo');
        $this->assertEquals(SecretTypes::PASSWORD, $key->getType());
        $this->assertEquals('foo', $key->getValue());
    }
}
