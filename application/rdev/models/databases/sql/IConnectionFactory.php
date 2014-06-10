<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for connection factories to implement
 */
namespace RDev\Models\Databases\SQL;

interface IConnectionFactory
{
    /**
     * Creates a database connection
     *
     * @param Server $server The server to connect to
     * @return IConnection The database connection
     */
    public function create(Server $server);
} 