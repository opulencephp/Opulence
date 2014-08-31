<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a database connection pool
 * This can handle multiple server setups or simple single server setups
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Configs;
use RDev\Models\Databases\SQL\Configs as SQLConfigs;

abstract class ConnectionPool
{
    /** @var array Maps driver names to their fully-qualified class names */
    public static $drivers = [
        "pdo_mysql" => "RDev\\Models\\Databases\\SQL\\PDO\\MySQL\\Driver",
        "pdo_pgsql" => "RDev\\Models\\Databases\\SQL\\PDO\\PostgreSQL\\Driver",
    ];
    /** @var array The servers in this pool */
    protected $servers = [
        "master" => null,
        "custom" => []
    ];
    /** @var IDriver The driver to use for connections made by this pool */
    protected $driver = null;
    /** @var array The list of connection options */
    protected $connectionOptions = [];
    /** @var array The list of driver options */
    protected $driverOptions = [];
    /** @var IConnection|null The connection to use for read queries */
    protected $readConnection = null;
    /** @var IConnection|null The connection to use for write queries */
    protected $writeConnection = null;

    /**
     * @param SQLConfigs\ConnectionPoolConfig|array $config The configuration to use to setup the connection pool
     *      It must contain the following keys:
     *          "driver" => name of the driver listed in self::$drivers OR
     *              The fully-qualified name of a custom driver class OR
     *              An object that implements IDriver
     *          "servers" => [
     *              "master" => An object that extends Server OR
     *                  An array that contains the following keys:
     *                      "host" => server host,
     *                      "username" => server username credential,
     *                      "password" => server password credential,
     *                      "databaseName" => name of database on server to use
     *                 The following keys are optional:
     *                      "port" => server port,
     *                      "charset" => character set
     *          ]
     *      The following are optional:
     *          "driverOptions" => settings to use to setup a driver connection,
     *          "connectionOptions" => the driver-specific connection settings
     * @throws \RuntimeException Thrown if the configuration was invalid
     */
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = $this->createConfigFromArray($config);
        }

        $this->driver = $config["driver"];
        $this->setServers($config["servers"]);
        $this->driverOptions = isset($config["driverOptions"]) ? $config["driverOptions"] : [];
        $this->connectionOptions = isset($config["connectionOptions"]) ? $config["connectionOptions"] : [];
    }

    /**
     * Gets the list of pre-defined driver names available in this class
     *
     * @return array The list of driver names
     */
    public static function getDriverNames()
    {
        return array_keys(self::$drivers);
    }

    /**
     * @return IDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return Server|null
     */
    public function getMaster()
    {
        return $this->servers["master"]["server"];
    }

    /**
     * Gets the connection used for read queries
     *
     * @param Server $preferredServer The preferred server to use
     * @return IConnection The connection to use for reads
     * @throws \RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    public function getReadConnection(Server $preferredServer = null)
    {
        if($preferredServer !== null)
        {
            $this->addServer("custom", $preferredServer);
            $this->setReadConnection($preferredServer);
        }
        elseif($this->readConnection == null)
        {
            $this->setReadConnection();
        }

        return $this->readConnection;
    }

    /**
     * Gets the connection used for write queries
     *
     * @param Server $preferredServer The preferred server to use
     * @return IConnection The connection to use for writes
     * @throws \RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    public function getWriteConnection(Server $preferredServer = null)
    {
        if($preferredServer != null)
        {
            $this->addServer("custom", $preferredServer);
            $this->setWriteConnection($preferredServer);
        }
        elseif($this->writeConnection == null)
        {
            $this->setWriteConnection();
        }

        return $this->writeConnection;
    }

    /**
     * @param Server $master
     */
    public function setMaster(Server $master)
    {
        $this->addServer("master", $master);
    }

    /**
     * Sets the connection to use for read queries
     *
     * @param Server $preferredServer The preferred server to connect to
     * @throws \RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    abstract protected function setReadConnection(Server $preferredServer = null);

    /**
     * Sets the connection to use for write queries
     *
     * @param Server $preferredServer The preferred server to connect to
     * @throws \RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    abstract protected function setWriteConnection(Server $preferredServer = null);

    /**
     * Adds a server to our list of servers
     *
     * @param string $type The type of server we're trying to add, eg "master", "custom"
     * @param Server $server The server to add
     */
    protected function addServer($type, Server $server)
    {
        switch($type)
        {
            case "master":
                $this->servers["master"] = ["server" => $server, "connection" => null];
                break;
            default:
                $serverHashId = spl_object_hash($server);

                if(!isset($this->servers[$type][$serverHashId]))
                {
                    $this->servers[$type][$serverHashId] = ["server" => $server, "connection" => null];
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
    protected function connectToServer(Server $server)
    {
        return $this->driver->connect($server, $this->connectionOptions, $this->driverOptions);
    }

    /**
     * Creates a config from an array to use for this pool
     *
     * @param array $configArray The array to create the config from
     * @return Configs\IConfig The config
     */
    protected function createConfigFromArray(array $configArray)
    {
        return new SQLConfigs\ConnectionPoolConfig($configArray);
    }

    /**
     * Gets a connection to the input server
     *
     * @param string $type The type of server we're trying to connect to, eg "master", "custom"
     * @param Server $server The server we want to connect to
     * @return IConnection The connection to the server
     * @throws \RuntimeException Thrown if the connection pool wasn't configured correctly
     */
    protected function getConnection($type, Server $server)
    {
        switch($type)
        {
            case "master":
                if($this->servers["master"]["server"] == null)
                {
                    throw new \RuntimeException("No master specified");
                }

                if($this->servers["master"]["connection"] == null)
                {
                    $this->servers["master"]["connection"] = $this->connectToServer($server);
                }

                return $this->servers["master"]["connection"];
            default:
                $serverHashId = spl_object_hash($server);

                if(!isset($this->servers[$type][$serverHashId])
                    || $this->servers[$type][$serverHashId]["server"] == null
                )
                {
                    throw new \RuntimeException("Server of type '" . $type . "' not added to connection pool");
                }

                if($this->servers[$type][$serverHashId]["connection"] == null)
                {
                    $this->servers[$type][$serverHashId]["connection"] = $this->connectToServer($server);
                }

                return $this->servers[$type][$serverHashId]["connection"];
        }
    }

    /**
     * Sets the server configuration
     *
     * @param array $config The configuration array to use to setup the list of servers used by this pool
     * @throws \RuntimeException Thrown if the config isn't valid
     */
    protected function setServers(array $config)
    {
        $this->setMaster($config["master"]);
    }
} 