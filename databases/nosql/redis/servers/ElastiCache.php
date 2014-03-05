<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RamODev\Databases\NoSQL\Redis\Servers;
use RamODev\Configs;
use RamODev\Databases\NoSQL\Redis;

class ElastiCache extends Redis\Server
{
    /** @var string The host of this server */
    protected $host = Configs\DatabaseConfig::ELASTICACHE_HOST;
}