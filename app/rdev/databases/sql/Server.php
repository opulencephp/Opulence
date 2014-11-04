<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides a skeleton for database servers to extend
 */
namespace RDev\Databases\SQL;
use RDev\Databases;

class Server extends Databases\Server
{
    /** @var string The username to log in to the server */
    protected $username = "";
    /** @var string The password to log in to the server */
    protected $password = "";
    /** @var string The name of the database to connect to on the server */
    protected $databaseName = "";
    /** @var string The character set used by this server */
    protected $charset = "utf8";

    /**
     * @param string $host The server host
     * @param string $username The username to log in to the server
     * @param string $password The password to log in to the server
     * @param string $databaseName The name of the database to connect to
     * @param int $port The port of this server
     * @param string $charset The character set used by this server
     */
    public function __construct(
        $host = null,
        $username = null,
        $password = null,
        $databaseName = null,
        $port = null,
        $charset = null
    )
    {
        if($host !== null)
        {
            $this->setHost($host);
        }

        if($username !== null)
        {
            $this->setUsername($username);
        }

        if($password !== null)
        {
            $this->setPassword($password);
        }

        if($databaseName !== null)
        {
            $this->setDatabaseName($databaseName);
        }

        if($port !== null)
        {
            $this->setPort($port);
        }

        if($charset !== null)
        {
            $this->setCharset($charset);
        }
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

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
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
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