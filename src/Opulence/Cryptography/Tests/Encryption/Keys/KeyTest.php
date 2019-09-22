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

use Opulence\Cryptography\Encryption\Keys\Key;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;

/**
 * Tests the key
 */
class KeyTest extends \PHPUnit\Framework\TestCase
{
    public function testValidKey(): void
    {
        $value = str_repeat('a', 32);
        $key = new Key($value);
        $this->assertEquals(SecretTypes::KEY, $key->getType());
        $this->assertEquals($value, $key->getValue());
    }
}
