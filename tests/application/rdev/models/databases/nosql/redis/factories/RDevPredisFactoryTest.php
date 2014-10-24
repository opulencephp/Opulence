<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Predis factory
 */
namespace RDev\Models\Databases\NoSQL\Redis\Factories;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\NoSQL\Redis\Configs;
use RDev\Tests\Models\Databases\NoSQL\Redis\Factories\Mocks;

class RDevPredisFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\RDevPredisFactory The factory to use to create Redis objects */
    private $rDevPredisFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->rDevPredisFactory = new Mocks\RDevPredisFactory();
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
        $redis = $this->rDevPredisFactory->createFromConfig($config);
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
        $redis = $this->rDevPredisFactory->createFromConfig($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\TypeMapper", $redis->getTypeMapper());
    }
}