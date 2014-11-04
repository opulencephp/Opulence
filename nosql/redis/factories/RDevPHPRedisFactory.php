<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the factory that instantiates PHPRedis objects
 */
namespace RDev\Databases\NoSQL\Redis\Factories;
use RDev\Databases\NoSQL\Redis;
use RDev\Databases\NoSQL\Redis\Configs;

class RDevPHPRedisFactory
{
    /**
     * Creates an instance of PHPRedis from a config
     *
     * @param Configs\ServerConfig $config The config to instantiate from
     * @return Redis\RDevPHPRedis The instantiated PHPRedis object
     */
    public function createFromConfig(Configs\ServerConfig $config)
    {
        /** @var Redis\Server $server */
        $server = $config["servers"]["master"];
        $typeMapper = new Redis\TypeMapper();

        return new Redis\RDevPHPRedis($server, $typeMapper);
    }
} 