<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Memcached type mapper class
 */
namespace RDev\Models\Databases\NoSQL\Memcached;

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
     * Tests converting from a Memcached timestamp
     */
    public function testConvertingFromMemcachedTimestamp()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->getTimestamp(),
            $this->typeMapper->fromMemcachedTimestamp($time->getTimestamp())->getTimestamp());
    }

    /**
     * Tests converting to a Memcached timestamp
     */
    public function testConvertingToMemcachedTimestamp()
    {
        $time = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->assertEquals($time->getTimestamp(), $this->typeMapper->toMemcachedTimestamp($time));
    }
} 