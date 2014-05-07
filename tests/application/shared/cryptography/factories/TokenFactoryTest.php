<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token factory
 */
namespace RamODev\Application\Shared\Cryptography\Factories;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating an even-length token and checking its length
     */
    public function testEvenRandomStringLength()
    {
        $tokenFactory = new TokenFactory();
        $tokenLength = 64;
        $randomString = $tokenFactory->createRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }

    /**
     * Tests that the token that's generated is the correct type
     */
    public function testInstanceOf()
    {
        $tokenFactory = new TokenFactory();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Cryptography\\Token",
            $tokenFactory->createToken(new \DateTime("now", new \DateTimeZone("utc")), new \DateTime("now",
                new \DateTimeZone("utc"))));
    }

    /**
     * Tests creating an odd-length token and checking its length
     */
    public function testOddTokenLength()
    {
        $tokenFactory = new TokenFactory();
        $tokenLength = 63;
        $randomString = $tokenFactory->createRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }
} 