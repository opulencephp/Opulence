<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the token class
 */
namespace RamODev\Application\Shared\Cryptography;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests seeing if a token with a valid-from value in the future is expired
     */
    public function testCheckingExpirationWithFutureValidFrom()
    {
        $validFrom = new \DateTime("+1 day", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, $validFrom, $validTo);
        $this->assertTrue($token->isExpired());
    }

    /**
     * Tests seeing if a token with a valid-to value in the future is expired
     */
    public function testCheckingExpirationWithFutureValidTo()
    {
        $validFrom = new \DateTime("now", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, $validFrom, $validTo);
        $this->assertFalse($token->isExpired());
    }

    /**
     * Tests seeing if a token with a valid-from value in the past is expired
     */
    public function testCheckingExpirationWithPastValidFrom()
    {
        $validFrom = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("+1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, $validFrom, $validTo);
        $this->assertFalse($token->isExpired());
    }

    /**
     * Tests seeing if a token with a valid-to value in the past is expired
     */
    public function testCheckingExpirationWithPastValidTo()
    {
        $validFrom = new \DateTime("now", new \DateTimeZone("UTC"));
        $validTo = new \DateTime("-1 week", new \DateTimeZone("UTC"));
        $token = new Token(1, $validFrom, $validTo);
        $this->assertTrue($token->isExpired());
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $id = 123;
        $token = new Token($id, new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $this->assertEquals($id, $token->getId());
    }

    /**
     * Tests getting the valid-from date
     */
    public function testGettingValidFromDate()
    {
        $validFromDate = new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC"));
        $token = new Token(1, $validFromDate, new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $this->assertEquals($validFromDate, $token->getValidFrom());
    }

    /**
     * Tests getting the valid-to date
     */
    public function testGettingValidToDate()
    {
        $validToDate = new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC"));
        $token = new Token(1, new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), $validToDate);
        $this->assertEquals($validToDate, $token->getValidTo());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $oldId = 1;
        $newId = 2;
        $token = new Token($oldId, new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $token->setId($newId);
        $this->assertEquals($newId, $token->getId());
    }
} 