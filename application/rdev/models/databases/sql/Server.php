<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides a skeleton for database servers to extend
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases;

abstract class Server extends Databases\Server
{
    /** @var string The username to log in to the server */
    protected $username = "";
    /** @var string The password to log in to the server */
    protected $password = "";
    /** @var string The name of the database to connect to on the server */
    protected $databaseName = "";

    /**
     * Gets the connection string
     *
     * @return string The string we can use to connect with
     */
    abstract public function getConnectionString();

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $databaseName
     */
    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
} 