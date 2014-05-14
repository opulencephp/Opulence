<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the skeleton for a cache server
 */
namespace RamODev\Application\Shared\Models\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Models\Databases;

abstract class Server extends Databases\Server
{
    /** @var int The port this server listens on */
    protected $port = 6379;
    /** @var int The default lifetime for items on this server */
    protected $lifetime = 86400;

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
}