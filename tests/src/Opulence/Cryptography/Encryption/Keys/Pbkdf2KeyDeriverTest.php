<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption\Keys;

use InvalidArgumentException;

/**
 * Tests the PBKDF2 key deriver
 */
class Pbkdf2KeyDeriverTest extends \PHPUnit\Framework\TestCase
{
    /** @var Pbkdf2KeyDeriver The key deriver to use in tests */
    private $keyDeriver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->keyDeriver = new Pbkdf2KeyDeriver();
    }

    /**
     * Test deriving keys from a key
     */
    public function testDerivingKeysFromKey()
    {
        $salt = random_bytes(IKeyDeriver::SALT_NUM_BYTES);
        $keyLengths = [16, 24, 32];
        
        foreach ($keyLengths as $keyLength) {
            $key = str_repeat("a", $keyLength);
            $keys = $this->keyDeriver->deriveKeysFromKey($key, $salt, $keyLength);
            $this->assertEquals($keyLength, mb_strlen($keys->getAuthenticationKey(), "8bit"));
            $this->assertEquals($keyLength, mb_strlen($keys->getEncryptionKey(), "8bit"));
            $this->assertNotEquals($keys->getAuthenticationKey(), mb_strlen($keys->getEncryptionKey()));
        }
    }

    /**
     * Test deriving keys from a password
     */
    public function testDerivingKeysFromPassword()
    {
        $salt = random_bytes(IKeyDeriver::SALT_NUM_BYTES);
        $keyLengths = [16, 24, 32];
        
        foreach ($keyLengths as $keyLength) {
            $keys = $this->keyDeriver->deriveKeysFromPassword("foo", $salt, $keyLength);
            $this->assertEquals($keyLength, mb_strlen($keys->getAuthenticationKey(), "8bit"));
            $this->assertEquals($keyLength, mb_strlen($keys->getEncryptionKey(), "8bit"));
            $this->assertNotEquals($keys->getAuthenticationKey(), mb_strlen($keys->getEncryptionKey()));
        }
    }

    /**
     * Tests that an invalid salt length throws an exception
     */
    public function testInvalidSaltLengthThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->keyDeriver->deriveKeysFromPassword("foo", "bar", 512);
    }
}