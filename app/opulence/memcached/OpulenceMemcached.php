<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an extension of Memcached
 */
namespace Opulence\Memcached;
use Memcached;

class OpulenceMemcached extends Memcached
{
    /** @var Server[] The server we're connecting to */
    protected $servers = null;
    /** @var TypeMapper The type mapper to use for converting data to/from Redis */
    protected $typeMapper = null;

    /**
     * @param TypeMapper $typeMapper The type mapper to use
     */
    public function __construct(TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function addServer($host, $port, $weight = 0)
    {
        $server = new Server();
        $server->setHost($host);
        $server->setPort($port);
        $server->setWeight($weight);
        $this->servers[] = $server;

        parent::addServer($host, $port, $weight);
    }

    /**
     * @inheritdoc
     */
    public function addServers(array $servers)
    {
        foreach($servers as $serverArray)
        {
            $this->addServer($serverArray[0], $serverArray[1], $serverArray[2]);
        }
    }

    /**
     * @return Server[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @return TypeMapper
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
} 