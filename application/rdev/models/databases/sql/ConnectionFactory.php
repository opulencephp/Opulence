<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the factory used to create database connections
 */
namespace RDev\Models\Databases\SQL;

class ConnectionFactory
{
    /** @var IDriver The driver to use for connections made by this factory */
    private $driver = null;
    /** @var array The list of connection options */
    private $connectionOptions = [];
    /** @var array The list of driver options */
    private $driverOptions = [];

    /**
     * @param IDriver $driver The driver to use for connections made by this factory
     * @param array $connectionOptions The list of connection options
     * @param array $driverOptions The list of driver options
     */
    public function __construct(IDriver $driver, array $connectionOptions = [], array $driverOptions = [])
    {
        $this->driver = $driver;
        $this->connectionOptions = $connectionOptions;
        $this->driverOptions = $driverOptions;
    }

    /**
     * Creates a database connection
     *
     * @param Server $server The server to connect to
     * @return IConnection The database connection
     */
    public function connect(Server $server)
    {
        return $this->driver->connect($server, $this->connectionOptions, $this->driverOptions);
    }
} 