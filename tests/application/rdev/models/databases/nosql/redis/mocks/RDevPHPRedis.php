<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the RDev PHP Redis class
 */
namespace RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;
use RDev\Models\Databases\NoSQL\Redis;

class RDevPHPRedis extends Redis\RDevPHPRedis
{
    /**
     * @param Redis\Server $server The server to connect to
     */
    public function __construct(Redis\Server $server)
    {
        $this->server = new $server;
        $this->typeMapper = new Redis\TypeMapper();
    }

    /**
     * We don't want to close the connection because there wasn't one, so do nothing
     */
    public function __destruct()
    {
        // Do nothing
    }
} 