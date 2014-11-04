<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Memcached server
 */
namespace RDev\Databases\NoSQL\Memcached;
use RDev\Databases;

class Server extends Databases\Server
{
    /** {@inheritdoc} */
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
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
} 