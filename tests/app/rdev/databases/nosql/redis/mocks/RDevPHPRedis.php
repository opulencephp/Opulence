<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the RDev PHP Redis class for use in testing
 */
namespace RDev\Tests\Databases\NoSQL\Redis\Mocks;
use RDev\Databases\NoSQL\Redis;
use RDev\Databases\NoSQL\Redis\Configs;

// To get around having to install Redis just to run tests, include a mock Redis class
if(!class_exists("Redis"))
{
    require_once(__DIR__ . "/Redis.php");
}

class RDevPHPRedis extends Redis\RDevPHPRedis
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Redis\Server $server, Redis\TypeMapper $typeMapper)
    {
        $this->server = $server;
        $this->typeMapper = $typeMapper;
    }

    /**
     * We don't want to close the connection because there wasn't one, so do nothing
     */
    public function __destruct()
    {
        // Do nothing
    }
} 