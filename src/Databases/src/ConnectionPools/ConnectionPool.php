<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\ConnectionPools;

use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Databases\IConnection;
use Opulence\Databases\IDriver;
use Opulence\Databases\Server;
use RuntimeException;

/**
 * Defines a database connection pool
 * This can handle multiple server setups or simple single server setups
 */
abstract class ConnectionPool
{
    /** @var array Maps driver names to their fully-qualified class names */
    public static array $drivers = [
        'pdo_mysql' => MySqlDriver::class,
        'pdo_pgsql' => PostgreSqlDriver::class,
    ];
    /** @var array The servers in this pool */
    protected array $servers = [
        'master' => [
            'server' => null,
            'connection' => null,
        ],
        'custom' => []
    ];
    /** @var IDriver The driver to use for connections made by this pool */
    protected IDriver $driver;
    /** @var array The list of connection options */
    protected array $connectionOptions = [];
    /** @var array The list of driver options */
    protected array $driverOptions = [];
    /** @var IConnection|null The connection to use for read queries */
    protected ?IConnection $readConnection = null;
    /** @var IConnection|null The connection to use for write queries */
    protected ?IConnection $writeConnection = null;

    /**
     * @param IDriver $driver The driver to use
     * @param Server $master The master server
     * @param array $driverOptions The setting to use to setup a driver
     * @param array $connectionOptions The driver-specific connection settings
     */
    public function __construct(
        IDriver $driver,
        Server $master,
        array $driverOptions = [],
        array $connectionOptions = []
    ) {
        $this->driver = $driver;
        $this->setMaster($master);
        $this->driverOptions = $driverOptions;
        $this->connectionOptions = $connectionOptions;
    }

    /**
     * Gets the list of pre-defined driver names available in this class
     *
     * @return array The list of driver names
     */
    public static function getDriverNames(): array
    {
        return array_keys(self::$drivers);
    }

    /**
     * @return IDriver
     */
    public function getDriver(): IDriver
    {
        return $this->driver;
    }

    /**
     * @return Server|null
     */
    public function getMaster(): ?Server
    {
        return $this->servers['master']['server'];
    }

    /**
     * Gets the connection used for read queries
     *
     * @param Server $preferredServer The preferred server to use
     * @return IConnection The connection to use for reads
     * @throws RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    public function getReadConnection(Server $preferredServer = null): IConnection
    {
        if ($preferredServer !== null) {
            $this->addServer('custom', $preferredServer);
            $this->setReadConnection($preferredServer);
        } elseif ($this->readConnection === null) {
            $this->setReadConnection();
        }

        return $this->readConnection;
    }

    /**
     * Gets the connection used for write queries
     *
     * @param Server $preferredServer The preferred server to use
     * @return IConnection The connection to use for writes
     * @throws RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    public function getWriteConnection(Server $preferredServer = null): IConnection
    {
        if ($preferredServer !== null) {
            $this->addServer('custom', $preferredServer);
            $this->setWriteConnection($preferredServer);
        } elseif ($this->writeConnection === null) {
            $this->setWriteConnection();
        }

        return $this->writeConnection;
    }

    /**
     * @param Server $master
     */
    public function setMaster(Server $master): void
    {
        $this->addServer('master', $master);
    }

    /**
     * Sets the connection to use for read queries
     *
     * @param Server $preferredServer The preferred server to connect to
     * @throws RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    abstract protected function setReadConnection(Server $preferredServer = null): void;

    /**
     * Sets the connection to use for write queries
     *
     * @param Server $preferredServer The preferred server to connect to
     * @throws RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    abstract protected function setWriteConnection(Server $preferredServer = null): void;

    /**
     * Adds a server to our list of servers
     *
     * @param string $type The type of server we're trying to add, eg "master", "custom"
     * @param Server $server The server to add
     */
    protected function addServer(string $type, Server $server): void
    {
        switch ($type) {
            case 'master':
                $this->servers['master'] = ['server' => $server, 'connection' => null];

                break;
            default:
                $serverHashId = spl_object_hash($server);

                if (!isset($this->servers[$type][$serverHashId])) {
                    $this->servers[$type][$serverHashId] = ['server' => $server, 'connection' => null];
                }

                break;
        }
    }

    /**
     * Creates a database connection
     *
     * @param Server $server The server to connect to
     * @return IConnection The database connection
     */
    protected function connectToServer(Server $server): IConnection
    {
        return $this->driver->connect($server, $this->connectionOptions, $this->driverOptions);
    }

    /**
     * Gets a connection to the input server
     *
     * @param string $type The type of server we're trying to connect to, eg "master", "custom"
     * @param Server $server The server we want to connect to
     * @return IConnection The connection to the server
     * @throws RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    protected function getConnection(string $type, Server $server): IConnection
    {
        switch ($type) {
            case 'master':
                if ($this->servers['master']['server'] === null) {
                    throw new RuntimeException('No master specified');
                }

                if ($this->servers['master']['connection'] instanceof IConnection) {
                    return $this->servers['master']['connection'];
                }

                $this->servers['master']['connection'] = $this->connectToServer($server);

                return $this->servers['master']['connection'];
            default:
                $serverHashId = spl_object_hash($server);

                if (!isset($this->servers[$type][$serverHashId])
                    || empty($this->servers[$type][$serverHashId]['server'])
                ) {
                    throw new RuntimeException("Server of type '" . $type . "' not added to connection pool");
                }

                if ($this->servers[$type][$serverHashId]['connection'] === null) {
                    $this->servers[$type][$serverHashId]['connection'] = $this->connectToServer($server);
                }

                return $this->servers[$type][$serverHashId]['connection'];
        }
    }
}
