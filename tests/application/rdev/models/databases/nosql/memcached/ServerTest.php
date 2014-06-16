<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Memcached server
 */
namespace RDev\Models\Databases\NoSQL\Memcached;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $reflectionObject = new \ReflectionObject($server);
        $property = $reflectionObject->getProperty("port");
        $property->setAccessible(true);
        $this->assertEquals($property->getValue($server), $server->getPort());
    }

    /**
     * Tests setting the weight
     */
    public function testSettingWeight()
    {
        $weight = 63;
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $server->setWeight($weight);
        $this->assertEquals($weight, $server->getWeight());
    }
} 