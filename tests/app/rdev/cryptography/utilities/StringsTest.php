<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the strings utility
 */
namespace RDev\Cryptography\Utilities;

class StringsTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals($tokenLength, strlen($randomString));
    }

    /**
     * Tests creating an odd-length token and checking its length
     */
    public function testOddTokenLength()
    {
        $tokenLength = 63;
        $randomString = $this->strings->generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }
}