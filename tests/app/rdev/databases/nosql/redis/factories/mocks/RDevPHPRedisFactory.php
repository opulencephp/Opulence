<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the factory to use to create mock PHPRedis objects in tests
 */
namespace RDev\Tests\Databases\NoSQL\Redis\Factories\Mocks;
use RDev\Databases\NoSQL\Redis;
use RDev\Databases\NoSQL\Redis\Configs;
use RDev\Databases\NoSQL\Redis\Factories;
use RDev\Tests\Databases\NoSQL\Redis\Mocks;

class RDevPHPRedisFactory extends Factories\RDevPHPRedisFactory
{
    /**
     * {@inheritdoc}
     * @return Mocks\RDevPHPRedis
     */
    public function createFromConfig(Configs\ServerConfig $config)
    {
        /** @var Redis\Server $server */
        $server = $config["servers"]["master"];
        $typeMapper = new Redis\TypeMapper();

        return new Mocks\RDevPHPRedis($server, $typeMapper);
    }
} 