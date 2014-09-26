<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token class
 */
namespace RDev\Models\Cryptography;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests seeing if a token with a valid-from value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidFrom()
    {
        $validFrom = new \DateTime("+1 day", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the future is expired
     */
    public function testCheckingIsActiveWithFutureValidTo()
    {
        $validFrom = new \DateTime("now", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests checking if a token is active when its active flag was set to false
     */
    public function testCheckingIsActiveWithInactiveToken()
    {
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, false);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-from value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidFrom()
    {
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $this->assertTrue($token->isActive());
    }

    /**
     * Tests seeing if a token with a valid-to value in the past is expired
     */
    public function testCheckingIsActiveWithPastValidTo()
    {
        $validFrom = new \DateTime("now", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $this->assertFalse($token->isActive());
    }

    /**
     * Tests creating an even-length token and checking its length
     */
    public function testEvenRandomStringLength()
    {
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $tokenLength = 64;
        $randomString = $token->generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }

    /**
     * Tests getting the hashed value from the token
     */
    public function testGettingHashedValue()
    {
        $hashedValue = "foo";
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, $hashedValue, $validFrom, $validTo, false);
        $this->assertEquals($hashedValue, $token->getHashedValue());
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $id = 1;
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token($id, "", $validFrom, $validTo, true);
        $this->assertEquals($id, $token->getId());
    }

    /**
     * Tests getting the valid-from date
     */
    public function testGettingValidFromDate()
    {
        $validFromDate = new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFromDate, new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")), true);
        $this->assertEquals($validFromDate, $token->getValidFrom());
    }

    /**
     * Tests getting the valid-to date
     */
    public function testGettingValidToDate()
    {
        $validToDate = new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC"));
        $token = new Token(1, "", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), $validToDate, true);
        $this->assertEquals($validToDate, $token->getValidTo());
    }

    /**
     * Tests creating an odd-length token and checking its length
     */
    public function testOddTokenLength()
    {
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, "", $validFrom, $validTo, true);
        $tokenLength = 63;
        $randomString = $token->generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $oldId = 1;
        $newId = 2;
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token($oldId, "", $validFrom, $validTo, true);
        $token->setId($newId);
        $this->assertEquals($newId, $token->getId());
    }

    /**
     * Tests verifying a correct hash
     */
    public function testVerifyingCorrectHash()
    {
        $unhashedValue = "foo";
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, Token::generateHashedValue($unhashedValue, PASSWORD_BCRYPT, 5), $validFrom, $validTo, false);
        $this->assertTrue($token->verify($unhashedValue));
    }

    /**
     * Tests verifying an incorrect hash
     */
    public function testVerifyingIncorrectHash()
    {
        $unhashedValue = "foo";
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, Token::generateHashedValue("bar", PASSWORD_BCRYPT, 5), $validFrom, $validTo, false);
        $this->assertFalse($token->verify($unhashedValue));
    }
} 