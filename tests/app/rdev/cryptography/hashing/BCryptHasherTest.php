<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the BCrypt hasher
 */
namespace RDev\Cryptography\Hashing;
use RDev\Cryptography\Utilities;

class BCryptHasherTest extends \PHPUnit_Framework_TestCase
{
    /** @var BCryptHasher The hasher to use in the tests */
    private $hasher = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->hasher = new BCryptHasher(new Utilities\Strings());
    }

    /**
     * Tests getting the default cost
     */
    public function testGettingDefaultCost()
    {
        $this->assertEquals(10, BCryptHasher::DEFAULT_COST);
    }

    /**
     * Tests a hash that doesn't need to be rehashed
     */
    public function testHashThatDoesNotNeedToBeRehashed()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 5]);
        $this->assertFalse($this->hasher->needsRehash($hashedValue, ["cost" => 5]));
    }

    /**
     * Tests a hash that needs to be rehashed
     */
    public function testHashThatNeedsToBeRehashed()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 5]);
        $this->assertTrue($this->hasher->needsRehash($hashedValue, ["cost" => 6]));
    }

    /**
     * Tests verifying a correct hash
     */
    public function testVerifyingCorrectHash()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 4]);
        $this->assertTrue(BCryptHasher::verify($hashedValue, "foo"));
    }

    /**
     * Tests verifying a correct hash with a pepper
     */
    public function testVerifyingCorrectHashWithPepper()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 4], "pepper");
        $this->assertTrue(BCryptHasher::verify($hashedValue, "foo", "pepper"));
    }

    /**
     * Tests verifying an incorrect hash
     */
    public function testVerifyingIncorrectHash()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 4]);
        $this->assertFalse(BCryptHasher::verify($hashedValue, "bar"));
    }

    /**
     * Tests verifying an incorrect hash with a pepper
     */
    public function testVerifyingIncorrectHashWithPepper()
    {
        $hashedValue = $this->hasher->generate("foo", ["cost" => 4], "pepper");
        $this->assertFalse(BCryptHasher::verify($hashedValue, "bar"));
    }
} 