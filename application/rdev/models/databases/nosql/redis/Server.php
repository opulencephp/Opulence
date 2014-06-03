<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases;

abstract class Server extends Databases\Server
{
    /** @var int The port this server listens on */
    protected $port = 6379;

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
}