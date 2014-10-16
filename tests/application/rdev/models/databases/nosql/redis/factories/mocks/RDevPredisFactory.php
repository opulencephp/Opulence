<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the factory to use to create mock Predis objects in tests
 */
namespace RDev\Tests\Models\Databases\NoSQL\Redis\Factories\Mocks;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\NoSQL\Redis\Configs;
use RDev\Models\Databases\NoSQL\Redis\Factories;
use RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;

class RDevPredisFactory extends Factories\RDevPredisFactory
{
    /**
     * {@inheritdoc}
     * @return Mocks\RDevPredis
     */
    public function createFromConfig(Configs\ServerConfig $config)
    {
        /** @var Redis\Server $server */
        $server = $config["servers"]["master"];
        $typeMapper = new Redis\TypeMapper();

        return new Mocks\RDevPredis($server, $typeMapper);
    }
} 