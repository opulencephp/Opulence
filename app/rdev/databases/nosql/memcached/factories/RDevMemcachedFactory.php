<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Memcached factory
 */
namespace RDev\Databases\NoSQL\Memcached\Factories;
use RDev\Databases\NoSQL\Memcached;
use RDev\Databases\NoSQL\Memcached\Configs;

class RDevMemcachedFactory
{
    /**
     * Creates an instance of Memcached from a config
     *
     * @param Configs\ServerConfig $config The config to instantiate from
     * @return Memcached\RDevMemcached The instantiated Memcached object
     */
    public function createFromConfig(Configs\ServerConfig $config)
    {
        $typeMapper = new Memcached\TypeMapper();
        $memcached = new Memcached\RDevMemcached($typeMapper);

        /** @var Memcached\Server $server */
        foreach($config["servers"] as $server)
        {
            $memcached->addServer($server->getHost(), $server->getPort(), $server->getWeight());
        }

        return $memcached;
    }
} 