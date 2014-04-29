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
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $id = 123;
        $token = new Token($id, "foo", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $this->assertEquals($id, $token->getId());
    }

    /**
     * Tests getting the valid-from date
     */
    public function testGettingValidFromDate()
    {
        $validFromDate = new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC"));
        $token = new Token(1, "foo", $validFromDate, new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $this->assertEquals($validFromDate, $token->getValidFrom());
    }

    /**
     * Tests getting the valid-to date
     */
    public function testGettingValidToDate()
    {
        $validToDate = new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC"));
        $token = new Token(1, "foo", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), $validToDate);
        $this->assertEquals($validToDate, $token->getValidTo());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $value = "foobar";
        $token = new Token(1, $value, new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $this->assertEquals($value, $token->getValue());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $oldId = 1;
        $newId = 2;
        $token = new Token($oldId, "foo", new \DateTime("1776-07-04 12:34:56", new \DateTimeZone("UTC")), new \DateTime("1970-01-01 01:00:00", new \DateTimeZone("UTC")));
        $token->setId($newId);
        $this->assertEquals($newId, $token->getId());
    }
} 