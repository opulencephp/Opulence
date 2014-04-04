<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RamODev\Application\TBA\Databases\NoSQL\Redis\Servers;
use RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\TBA\Configs;

class ElastiCache extends Redis\Server
{
    /** @var string The host of this server */
    protected $host = Configs\DatabaseConfig::ELASTICACHE_HOST;
}