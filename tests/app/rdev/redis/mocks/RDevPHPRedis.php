<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the RDev PHP Redis class for use in testing
 */
namespace RDev\Tests\Redis\Mocks;
use RDev\Redis\RDevPHPRedis as BaseRDevPHPRedis;
use RDev\Redis\Server;
use RDev\Redis\TypeMapper;

// To get around having to install Redis just to run tests, include a mock Redis class
if(!class_exists("Redis"))
{
    require __DIR__ . "/Redis.php";
}

class RDevPHPRedis extends BaseRDevPHPRedis
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Server $server, TypeMapper $typeMapper)
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

    /**
     * {@inheritdoc}
     */
    public function select($database)
    {
        // Don't actually select the database in Redis
        $this->server->setDatabaseIndex($database);
    }
} 