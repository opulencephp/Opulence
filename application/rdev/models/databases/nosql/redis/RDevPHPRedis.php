<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the PHPRedis library
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Exceptions;

class RDevPHPRedis extends \Redis implements IRedis
{
    use TRedis;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->typeMapper = new TypeMapper();

        parent::connect($this->server->getHost(), $this->server->getPort(), $this->server->getConnectionTimeout());
        parent::select($this->server->getDatabaseIndex());

        if($server->passwordIsSet())
        {
            parent::auth($server->getPassword());
        }
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        parent::close();
    }
}