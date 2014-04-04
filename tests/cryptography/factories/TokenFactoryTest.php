<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token factory
 */
namespace RamODev\Application\Cryptography\Factories;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating a token
     */
    public function testCreatingToken()
    {
        $tokenFactory = new TokenFactory();
        $this->assertInstanceOf("RamODev\\Application\\Cryptography\\Token", $tokenFactory->createToken("foo", new \DateTime("now", new \DateTimeZone("UTC")), "pepper", "bar"));
    }

    /**
     * Tests generating a new token
     */
    public function testGeneratingNewToken()
    {
        $tokenFactory = new TokenFactory();
        $this->assertInstanceOf("RamODev\\Application\\Cryptography\\Token", $tokenFactory->generateNewToken("pepper", "foo"));
    }
} 