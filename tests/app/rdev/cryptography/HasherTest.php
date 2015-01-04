<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the hasher class
 */
namespace RDev\Cryptography;

class HasherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating an even-length token and checking its length
     */
    public function testEvenTokenLength()
    {
        $tokenLength = 64;
        $randomString = Hasher::generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }

    /**
     * Tests creating an odd-length token and checking its length
     */
    public function testOddTokenLength()
    {
        $tokenLength = 63;
        $randomString = Hasher::generateRandomString($tokenLength);
        $this->assertEquals($tokenLength, strlen($randomString));
    }
} 