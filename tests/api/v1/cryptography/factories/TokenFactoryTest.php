<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token factory
 */
namespace RamODev\API\V1\Cryptography\Factories;

require_once(__DIR__ . "/../../../../../api/v1/cryptography/factories/TokenFactory.php");

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating a token
     */
    public function testCreatingToken()
    {
        $tokenFactory = new TokenFactory();
        $this->assertInstanceOf("RamODev\\API\\V1\\Cryptography\\Token", $tokenFactory->createToken("foo", new \DateTime("now", new \DateTimeZone("UTC")), "pepper", "bar"));
    }

    /**
     * Tests generating a new token
     */
    public function testGeneratingNewToken()
    {
        $tokenFactory = new TokenFactory();
        $this->assertInstanceOf("RamODev\\API\\V1\\Cryptography\\Token", $tokenFactory->generateNewToken("pepper", "foo"));
    }
} 