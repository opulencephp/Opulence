<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Memcached type mapper class
 */
namespace RDev\Memcached;
use DateTime;

class TypeMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var TypeMapper The type mapper to use for tests */
    private $typeMapper = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->typeMapper = new TypeMapper();
    }

    /**
     * Tests converting from a false Memcached boolean
     */
    public function testConvertingFromFalseMemcachedBoolean()
    {
        $this->assertSame(false, $this->typeMapper->fromMemcachedBoolean(0));
    }

    /**
     * Tests converting from a Memcached timestamp
     */
    public function testConvertingFromMemcachedTimestamp()
    {
        $time = new DateTime("now");
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromMemcachedTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting from a true Memcached boolean
     */
    public function testConvertingFromTrueMemcachedBoolean()
    {
        $this->assertSame(true, $this->typeMapper->fromMemcachedBoolean(1));
    }

    /**
     * Tests converting to a false Memcached boolean
     */
    public function testConvertingToFalseMemcachedBoolean()
    {
        $this->assertSame(0, $this->typeMapper->toMemcachedBoolean(false));
    }

    /**
     * Tests converting to a Memcached timestamp
     */
    public function testConvertingToMemcachedTimestamp()
    {
        $time = new DateTime("now");
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toMemcachedTimestamp($time));
    }

    /**
     * Tests converting to a true Memcached boolean
     */
    public function testConvertingToTrueMemcachedBoolean()
    {
        $this->assertSame(1, $this->typeMapper->toMemcachedBoolean(true));
    }
} 