<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption\Keys;

/**
 * Tests the derived keys
 */
class DerivedKeysTest extends \PHPUnit\Framework\TestCase
{
    /** @var DerivedKeys The derived keys to use in tests */
    private $derivedKeys = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->derivedKeys = new DerivedKeys(str_repeat('1', 32), str_repeat('2', 32));
    }

    /**
     * Tests getting the authentication key
     */
    public function testGettingAuthenticationKey()
    {
        $this->assertEquals(str_repeat('2', 32), $this->derivedKeys->getAuthenticationKey());
    }

    /**
     * Tests getting the encryption key
     */
    public function testGettingEncryptionKey()
    {
        $this->assertEquals(str_repeat('1', 32), $this->derivedKeys->getEncryptionKey());
    }
}
