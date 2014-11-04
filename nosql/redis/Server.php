<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis server
 */
namespace RDev\Databases\NoSQL\Redis;
use RDev\Databases;

class Server extends Databases\Server
{
    /** {@inheritdoc} */
    protected $port = 6379;
    /** @var string|null The optional password to use to authenticate the connection */
    protected $password = null;
    /** @var int The index of the database to select when connected */
    protected $databaseIndex = 0;
    /** @var int The connection timeout (in seconds) */
    protected $connectionTimeout = 0;

    /**
     * @param string $host The server host
     * @param string $password The password to log in to the server
     * @param int $port The port of this server
     * @param int $databaseIndex The name of the database to connect to
     * @param int $connectionTimeout The connection timeout (in seconds)
     */
    public function __construct(
        $host = null,
        $password = null,
        $port = null,
        $databaseIndex = null,
        $connectionTimeout = null
    )
    {
        if($host !== null)
        {
            $this->setHost($host);
        }

        if($password !== null)
        {
            $this->setPassword($password);
        }

        if($port !== null)
        {
            $this->setPort($port);
        }

        if($databaseIndex !== null)
        {
            $this->setDatabaseIndex($databaseIndex);
        }

        if($connectionTimeout !== null)
        {
            $this->setConnectionTimeout($connectionTimeout);
        }
    }

    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * @return int
     */
    public function getDatabaseIndex()
    {
        return $this->databaseIndex;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Gets whether or not the password has been set
     *
     * @return bool True if the password is set, otherwise false
     */
    public function passwordIsSet()
    {
        return $this->password !== null;
    }

    /**
     * @param int $connectionTimeout
     */
    public function setConnectionTimeout($connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * @param int $databaseIndex
     */
    public function setDatabaseIndex($databaseIndex)
    {
        $this->databaseIndex = $databaseIndex;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}