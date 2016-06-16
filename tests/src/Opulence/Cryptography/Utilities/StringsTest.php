<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Utilities;

/**
 * Tests the strings utility
 */
class StringsTest extends \PHPUnit\Framework\TestCase
{
    /** @var Strings The string utility to use in tests */
    private $strings = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->strings = new Strings();
    }

    /**
     * Tests checking equal strings
     */
    public function testCheckingEqualStrings()
    {
        $this->assertTrue($this->strings->isEqual("foobar", "foobar"));
    }

    /**
     * Tests checking unequal strings
     */
    public function testCheckingUnequalStrings()
    {
        $this->assertFalse($this->strings->isEqual("foobar", "foo"));
    }

    /**
     * Tests creating an even-length token and checking its length
     */
    public function testEvenTokenLength()
    {
        $tokenLength = 64;
        $randomString = $this->strings->generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, mb_strlen($randomString));
    }
    
    /**
     * Tests generating a UUID V4
     */
    public function testGeneratingUuidV4()
    {
        $uuid1 = $this->strings->generateUuidV4();
        $uuid2 = $this->strings->generateUuidV4();
        $this->assertNotEquals($uuid1, $uuid2);
        $this->assertEquals(36, strlen($uuid1));
        $this->assertEquals(36, strlen($uuid2));
    }

    /**
     * Tests creating an odd-length token and checking its length
     */
    public function testOddTokenLength()
    {
        $tokenLength = 63;
        $randomString = $this->strings->generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, mb_strlen($randomString));
    }

    /**
     * Tests that random bytes are generated
     */
    public function testRandomBytesAreGenerated()
    {
        $this->assertEquals(32, mb_strlen($this->strings->generateRandomBytes(32), "8bit"));
    }
}