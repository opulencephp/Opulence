<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a database
 */
namespace RamODev\Databases;

abstract class Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;

    /**
     * @param Server $server The storage server to connect to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Closes the connection
     */
    abstract public function close();

    /**
     * Attempts to connect to the server
     *
     * @return bool True if we connected successfully, otherwise false
     */
    abstract public function connect();

    /**
     * Gets whether or not we're connected
     *
     * @return bool Whether or not we're connected
     */
    abstract public function isConnected();
} 