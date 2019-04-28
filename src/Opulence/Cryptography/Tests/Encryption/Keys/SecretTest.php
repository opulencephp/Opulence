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

/**
 * Tests the secret
 */
class SecretTest extends \PHPUnit\Framework\TestCase
{
    /** @var Secret The secret to use in tests */
    private $secret;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->secret = new Secret(SecretTypes::PASSWORD, 'foo');
    }

    /**
     * Tests getting the type
     */
    public function testGettingType(): void
    {
        $this->assertEquals(SecretTypes::PASSWORD, $this->secret->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        $this->assertEquals('foo', $this->secret->getValue());
    }

    /**
     * Tests setting a valid key
     */
    public function testValidKey(): void
    {
        $key = str_repeat('a', 32);
        $secret = new Secret(SecretTypes::KEY, $key);
        $this->assertEquals($key, $secret->getValue());
    }
}
