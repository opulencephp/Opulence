<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis type mapper class
 */
namespace RDev\Models\Databases\NoSQL\Redis;

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
     * Tests converting from a Redis timestamp
     */
    public function testConvertingFromRedisTimestamp()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromRedisTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting to a Redis timestamp
     */
    public function testConvertingToRedisTimestamp()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toRedisTimestamp($time));
    }
} 