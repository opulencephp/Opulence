<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the base class for PDO drivers
 */
namespace Opulence\Databases\PDO;
use Opulence\Databases\IDriver;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;

abstract class Driver implements IDriver
{
    /** @var Provider The provider this driver uses */
    protected $provider = null;

    public function __construct()
    {
        $this->setProvider();
    }

    /**
     * {@inheritdoc}
     * @return Connection The PDO connection
     */
    public function connect(Server $server, array $connectionOptions = [], array $driverOptions = [])
    {
        $dsn = $this->getDSN($server, $connectionOptions);

        return new Connection($this->provider, $server, $dsn, $driverOptions);
    }

    /**
     * Gets the DSN string to connect to a server through PDO
     *
     * @param Server $server The server to connect to
     * @param array $options The list of driver-specific options
     * @return string The DSN to use to connect to PDO
     */
    abstract protected function getDSN(Server $server, array $options = []);

    /**
     * Sets the provider used by this driver's connections
     */
    abstract protected function setProvider();
} 