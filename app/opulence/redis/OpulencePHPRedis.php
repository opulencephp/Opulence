<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an extension of the PHPRedis library
 */
namespace Opulence\Redis;
use Redis;

class OpulencePHPRedis extends Redis implements IRedis
{
    use TRedis;

    /**
     * @param Server $server The server to use
     * @param TypeMapper $typeMapper The type mapper to use
     */
    public function __construct(Server $server, TypeMapper $typeMapper)
    {
        $this->server = $server;
        $this->typeMapper = $typeMapper;

        parent::connect($this->server->getHost(), $this->server->getPort(), $this->server->getConnectionTimeout());
        parent::select($this->server->getDatabaseIndex());

        if($this->server->passwordIsSet())
        {
            parent::auth($this->server->getPassword());
        }
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        parent::close();
    }

    /**
     * @inheritdoc
     */
    public function select($database)
    {
        parent::select($database);

        $this->server->setDatabaseIndex($database);
    }
}