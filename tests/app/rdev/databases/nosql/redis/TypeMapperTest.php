<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Redis type mapper class
 */
namespace RDev\Databases\NoSQL\Redis;

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
     * Tests converting from a false Redis boolean
     */
    public function testConvertingFromFalseRedisBoolean()
    {
        $this->assertSame(false, $this->typeMapper->fromRedisBoolean(0));
    }

    /**
     * Tests converting from a Redis timestamp
     */
    public function testConvertingFromRedisTimestamp()
    {
        $time = new \DateTime("now");
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromRedisTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting from a true Redis boolean
     */
    public function testConvertingFromTrueRedisBoolean()
    {
        $this->assertSame(true, $this->typeMapper->fromRedisBoolean(1));
    }

    /**
     * Tests converting to a false Redis boolean
     */
    public function testConvertingToFalseRedisBoolean()
    {
        $this->assertSame(0, $this->typeMapper->toRedisBoolean(false));
    }

    /**
     * Tests converting to a Redis timestamp
     */
    public function testConvertingToRedisTimestamp()
    {
        $time = new \DateTime("now");
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toRedisTimestamp($time));
    }

    /**
     * Tests converting to a true Redis boolean
     */
    public function testConvertingToTrueRedisBoolean()
    {
        $this->assertSame(1, $this->typeMapper->toRedisBoolean(true));
    }
} 