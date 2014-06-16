<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Redis\\Server");
        $reflectionObject = new \ReflectionObject($server);
        $property = $reflectionObject->getProperty("port");
        $property->setAccessible(true);
        $this->assertEquals($property->getValue($server), $server->getPort());
    }
} 