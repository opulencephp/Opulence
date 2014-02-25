<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Test the token
 */
namespace RamODev\API\V1\Cryptography;

require_once(__DIR__ . "/../../../../api/v1/cryptography/Token.php");

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /** @var Token The token to test */
    private $token = null;
    /** @var string The token to use */
    private $tokenString = "foo";
    /** @var string The formatted time to use for the expiration */
    private $expirationTimestamp = "1776-07-04 12:34:56";
    /** @var string The HMAC to use */
    private $hmac = "bar";

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->token = new Token($this->tokenString, new \DateTime($this->expirationTimestamp, new \DateTimeZone("UTC")), $this->hmac);
    }

    /**
     * Tests getting the expiration
     */
    public function testGettingExpiration()
    {
        $this->assertEquals(new \DateTime($this->expirationTimestamp, new \DateTimeZone("UTC")), $this->token->getExpiration());
    }

    /**
     * Tests getting the HMAC
     */
    public function testGettingHMAC()
    {
        $this->assertEquals($this->hmac, $this->token->getHMAC());
    }

    /**
     * Tests getting the token
     */
    public function testGettingToken()
    {
        $this->assertEquals($this->tokenString, $this->token->getTokenString());
    }
} 