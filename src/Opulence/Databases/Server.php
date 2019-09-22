<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases;

/**
 * Defines a database server
 */
class Server
{
    /** @var string The host of this server */
    protected string $host = '';
    /** @var int|null The port this server listens on */
    protected ?int $port = null;
    /** @var string The username to log in to the server */
    protected string $username = '';
    /** @var string The password to log in to the server */
    protected string $password = '';
    /** @var string The name of the database to connect to on the server */
    protected string $databaseName = '';
    /** @var string The character set used by this server */
    protected string $charset = 'utf8';

    /**
     * @param string $host The server host
     * @param string $username The username to log in to the server
     * @param string $password The password to log in to the server
     * @param string $databaseName The name of the database to connect to
     * @param int $port The port of this server
     * @param string $charset The character set used by this server
     */
    public function __construct(
        string $host = null,
        string $username = null,
        string $password = null,
        string $databaseName = null,
        int $port = null,
        string $charset = null
    ) {
        if ($host !== null) {
            $this->setHost($host);
        }

        if ($username !== null) {
            $this->setUsername($username);
        }

        if ($password !== null) {
            $this->setPassword($password);
        }

        if ($databaseName !== null) {
            $this->setDatabaseName($databaseName);
        }

        if ($port !== null) {
            $this->setPort($port);
        }

        if ($charset !== null) {
            $this->setCharset($charset);
        }
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $charset
     */
    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /**
     * @param string $databaseName
     */
    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
