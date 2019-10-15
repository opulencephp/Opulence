<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Tests\Encryption\Keys;

use Opulence\Cryptography\Encryption\Keys\Password;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;
use PHPUnit\Framework\TestCase;

/**
 * Tests the key
 */
class PasswordTest extends TestCase
{
    public function testGettingValue(): void
    {
        $key = new Password('foo');
        $this->assertEquals(SecretTypes::PASSWORD, $key->getType());
        $this->assertEquals('foo', $key->getValue());
    }
}
