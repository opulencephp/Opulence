<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the factory that instantiates Predis objects
 */
namespace RDev\Databases\NoSQL\Redis\Factories;
use RDev\Databases\NoSQL\Redis;
use RDev\Databases\NoSQL\Redis\Configs;

class RDevPredisFactory
{
    /**
     * Creates an instance of Predis from a config
     *
     * @param Configs\ServerConfig $config The config to instantiate from
     * @return Redis\RDevPredis The instantiated Predis object
     */
    public function createFromConfig(Configs\ServerConfig $config)
    {
        /** @var Redis\Server $server */
        $server = $config["servers"]["master"];
        $typeMapper = new Redis\TypeMapper();

        return new Redis\RDevPredis($server, $typeMapper);
    }
} 