<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a Memcached server
 */
namespace Opulence\Memcached;

class Server
{
    /** @var string The host of this server */
    protected $host = "";
    /** @var int The port this server listens on */
    protected $port = 11211;
    /** @var int The weight of this server relative to the total weight of all servers in the pool */
    protected $weight = 0;

    /**
     * @param string $host The server host
     * @param int $port The port of this server
     * @param int $weight The weight of this server, relative to the total weight of all the servers in the pool
     */
    public function __construct($host = null, $port = null, $weight = null)
    {
        if($host !== null)
        {
            $this->setHost($host);
        }

        if($port !== null)
        {
            $this->setPort($port);
        }

        if($weight !== null)
        {
            $this->setWeight($weight);
        }
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return int|null
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
} 