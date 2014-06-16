<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Memcached server
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Models\Databases;

class Server extends Databases\Server
{
    /** {@inheritdoc} */
    protected $port = 11211;
    /** @var int The weight of this server relative to the total weight of all servers in the pool */
    protected $weight = 0;

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