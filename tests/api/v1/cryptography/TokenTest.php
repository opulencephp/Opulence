<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Test the token
 */
namespace RamODev\API\V1\Cryptography;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /** @var Token The token to test */
    private $token = null;
    /** @var string The token to use */
    private $tokenString = "foo";
    /** @var string The formatted time to use for the expiration */
    private $expirationTimestamp = "1776-07-04 12:34:56";
    /** @var string The salt to use */
    private $salt = "pepper";
    /** @var string The secret key to use */
    private $secretKey = "bar";

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->token = new Token($this->tokenString, new \DateTime($this->expirationTimestamp, new \DateTimeZone("UTC")), $this->salt, $this->secretKey);
    }

    /**
     * Tests getting the expiration
     */
    public function testGettingExpiration()
    {
        $this->assertEquals(new \DateTime($this->expirationTimestamp, new \DateTimeZone("UTC")), $this->token->getExpiration());
    }

    /**
     * Tests getting the salt
     */
    public function testGettingSalt()
    {
        $this->assertEquals($this->salt, $this->token->getSalt());
    }

    /**
     * Tests getting the secret key
     */
    public function testGettingSecretKey()
    {
        $this->assertEquals($this->secretKey, $this->token->getSecretKey());
    }

    /**
     * Tests getting the token string
     */
    public function testGettingTokenString()
    {
        $this->assertEquals($this->tokenString, $this->token->getTokenString());
    }

    /**
     * Tests validating an HMAC
     */
    public function testHMACIsValid()
    {
        // We grab the token's HMAC, then pass it into the validation method
        $reflectionObject = new \ReflectionObject($this->token);
        $property = $reflectionObject->getProperty("hmac");
        $property->setAccessible(true);
        $hmac = $property->getValue($this->token);
        $this->assertTrue($this->token->hmacIsValid($hmac));
    }

    /**
     * Tests seeing if the token is expired
     */
    public function testIsExpired()
    {
        $this->assertTrue($this->token->isExpired());
    }
} 