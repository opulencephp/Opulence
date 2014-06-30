<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base class for PDO drivers
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Systems;

abstract class Driver implements SQL\IDriver
{
    /** @var Systems\System The system this driver uses */
    protected $system = null;

    public function __construct()
    {
        $this->setSystem();
    }

    /**
     * {@inheritdoc}
     * @return Connection The PDO connection
     */
    public function connect(SQL\Server $server, array $connectionOptions = [], array $driverOptions = [])
    {
        $dsn = $this->createDSN($server, $connectionOptions);

        return new Connection($this->system, $server, $dsn, $driverOptions);
    }

    /**
     * Creates the DSN string to connect to a server through PDO
     *
     * @param SQL\Server $server The server to connect to
     * @param array $options The list of driver-specific options
     * @return string The DSN to use to connect to PDO
     */
    abstract protected function createDSN(SQL\Server $server, array $options = []);

    /**
     * Sets the system used by this driver's connections
     */
    abstract protected function setSystem();
} 