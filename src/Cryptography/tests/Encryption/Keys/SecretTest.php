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

use Opulence\Cryptography\Encryption\Keys\Secret;
use Opulence\Cryptography\Encryption\Keys\SecretTypes;
use PHPUnit\Framework\TestCase;

/**
 * Tests the secret
 */
class SecretTest extends TestCase
{
    private Secret $secret;

    protected function setUp(): void
    {
        $this->secret = new Secret(SecretTypes::PASSWORD, 'foo');
    }

    public function testGettingType(): void
    {
        $this->assertEquals(SecretTypes::PASSWORD, $this->secret->getType());
    }

    public function testGettingValue(): void
    {
        $this->assertEquals('foo', $this->secret->getValue());
    }

    public function testValidKey(): void
    {
        $key = str_repeat('a', 32);
        $secret = new Secret(SecretTypes::KEY, $key);
        $this->assertEquals($key, $secret->getValue());
    }
}
