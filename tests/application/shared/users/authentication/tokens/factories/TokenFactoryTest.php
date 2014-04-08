<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token factory
 */
namespace RamODev\Application\Shared\Users\Authentication\Tokens\Factories;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the token that's generated is a string
     */
    public function testTokenIsString()
    {
        $tokenFactory = new TokenFactory();
        $this->assertTrue(is_string($tokenFactory->createToken(64)));
    }

    /**
     * Tests creating a token and checking its length
     */
    public function testTokenLength()
    {
        $tokenFactory = new TokenFactory();
        $tokenLength = 64;
        $token = $tokenFactory->createToken($tokenLength);
        $this->assertEquals($tokenLength, strlen($token));
    }
} 