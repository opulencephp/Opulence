<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a database connection pool
 * This can handle multiple server setups or simple single server setups
 */
namespace RDev\Models\Databases\SQL;

abstract class ConnectionPool
{
    /** @var array Maps driver names to their fully-qualified class names */
    public static $drivers = [
        "pdo_mysql" => "RDev\\Models\\Databases\\SQL\\PDO\\MySQL\\Driver",
        "pdo_postgresql" => "RDev\\Models\\Databases\\SQL\\PDO\\PostgreSQL\\Driver",
    ];
    /**
     * The configuration that holds data about the driver and servers to use in a database connection
     *
     * @var array
     */
    protected $servers = [
        "master" => null,
        "custom" => []
    ];
    /** @var IDriver The driver to use for connections made by this factory */
    protected $driver = null;
    /** @var array The list of connection options */
    protected $connectionOptions = [];
    /** @var array The list of driver options */
    protected $driverOptions = [];
    /** @var ServerFactory The factory to use to create servers from configs */
    protected $serverFactory = null;
    /** @var IConnection|null The connection to use for read queries */
    protected $readConnection = null;
    /** @var IConnection|null The connection to use for write queries */
    protected $writeConnection = null;

    /**
     * @param array $config The configuration array to use to setup the connection pool
     *      This must contain the following keys:
     *          "driver" => name of the driver listed in self::$drivers OR
     *              The fully-qualified name of a custom driver class OR
     *              An object that extends IDriver
     *          "servers" => [
     *              "master" => master server configuration (see ServerFactory for examples of a server configuration)
     *          ]
     *      The following options are optional:
     *          "driverOptions" => settings to use to setup a driver connection,
     *          "driverOptions" => the driver-specific connection settings
     * @throws \RuntimeException Thrown if the configuration was invalid
     * @see ServerFactory
     */
    public function __construct(array $config)
    {
        if(!$this->validateConfig($config))
        {
            throw new \RuntimeException("Invalid connection pool configuration");
        }

        if($config["driver"] instanceof IDriver)
        {
            $this->driver = $config["driver"];
        }
        elseif(isset(self::$drivers[$config["driver"]]))
        {
            $this->driver = new self::$drivers[$config["driver"]]();
        }
        else
        {
            // We assume this is a custom driver class
            if(!class_exists($config["driver"]))
            {
                throw new \RuntimeException("Invalid custom driver: " . $config["driver"]);
            }

            $this->driver = new $config["driver"]();
        }

        $this->serverFactory = new ServerFactory();
        $this->driverOptions = isset($config["driverOptions"]) ? $config["driverOptions"] : [];
        $this->connectionOptions = isset($config["connectionOptions"]) ? $config["connectionOptions"] : [];
        $this->initServersFromConfig($config);
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
     * Initializes the server configuration from an array
     *
     * @param array $config The configuration array to use to setup the list of servers used by this pool
     */
    protected function initServersFromConfig(array $config)
    {
        $this->setMaster($this->serverFactory->createFromConfig($config["servers"]["master"]));
    }

    /**
     * Validates the configuration array
     *
     * @param array $config The raw configuration passed into the constructor
     * @return bool True if the configuration is valid, otherwise false
     */
    protected function validateConfig(array $config)
    {
        $requiredFields = ["driver", "servers"];

        foreach($requiredFields as $requiredField)
        {
            if(!isset($config[$requiredField]))
            {
                return false;
            }
        }

        if(!isset($config["servers"]["master"]))
        {
            return false;
        }

        return true;
    }
} 