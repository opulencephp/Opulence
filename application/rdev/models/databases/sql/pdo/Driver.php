<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base class for PDO drivers
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Providers;

abstract class Driver implements SQL\IDriver
{
    /** @var Providers\Provider The provider this driver uses */
    protected $provider = null;

    public function __construct()
    {
        $this->setProvider();
    }

    /**
     * {@inheritdoc}
     * @return Connection The PDO connection
     */
    public function connect(SQL\Server $server, array $connectionOptions = [], array $driverOptions = [])
    {
        $dsn = $this->createDSN($server, $connectionOptions);

        return new Connection($this->provider, $server, $dsn, $driverOptions);
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
     * Sets the provider used by this driver's connections
     */
    abstract protected function setProvider();
} 