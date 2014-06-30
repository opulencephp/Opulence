<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of Memcached
 */
namespace RDev\Models\Databases\NoSQL\Memcached;

class RDevMemcached extends \Memcached
{
    /** @var Server The server we're connecting to */
    private $server = null;
    /** @var TypeMapper The type mapper to use for converting data to/from Redis */
    private $typeMapper = null;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->typeMapper = new TypeMapper();

        parent::__construct();
        parent::addServer($this->server->getHost(), $this->server->getPort(), $this->server->getWeight());
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return TypeMapper
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
} 