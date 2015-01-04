<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PHPRedis factory
 */
namespace RDev\Databases\NoSQL\Redis\Factories;
use RDev\Databases\NoSQL\Redis;
use RDev\Databases\NoSQL\Redis\Configs;
use RDev\Tests\Databases\NoSQL\Redis\Factories\Mocks;

class RDevPHPRedisFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\RDevPHPRedisFactory The factory to use to create Redis objects */
    private $rDevPHPRedisFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->rDevPHPRedisFactory = new Mocks\RDevPHPRedisFactory();
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Redis\Server();
        $configArray = [
            "servers" => [
                "master" => $server
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $redis = $this->rDevPHPRedisFactory->createFromConfig($config);
        $this->assertSame($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $configArray = [
            "servers" => [
                "master" => new Redis\Server()
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $redis = $this->rDevPHPRedisFactory->createFromConfig($config);
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Redis\\TypeMapper", $redis->getTypeMapper());
    }
}