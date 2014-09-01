<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of Memcached
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Models\Databases\NoSQL\Memcached\Configs;

class RDevMemcached extends \Memcached
{
    /** @var Server[] The server we're connecting to */
    protected $servers = null;
    /** @var TypeMapper The type mapper to use for converting data to/from Redis */
    protected $typeMapper = null;

    /**
     * @param Configs\ServerConfig|array $config The configuration to use for the server to connect to
     *      This must contain the following keys:
     *          "servers" => [
     *              "host" => server host,
     *              "port" => server port
     *          ]
     *      The following keys are optional in the servers:
     *          "weight" => the weight of the server relative to the total weight of all other servers
     */
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = new Configs\ServerConfig($config);
        }

        $this->typeMapper = new TypeMapper();

        parent::__construct();

        /** @var Server $server */
        foreach($config["servers"] as $server)
        {
            $this->addServer($server->getHost(), $server->getPort(), $server->getWeight());
        }
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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