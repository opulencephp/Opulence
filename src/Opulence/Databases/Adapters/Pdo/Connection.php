<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Databases\IConnection;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;
use PDO;
use PDOException;

/**
 * Defines an extension of the PDO library with lazy-connection
 * In other words, a database connection is only made if we absolutely need to, which gives us a performance gain
 */
class Connection implements IConnection
{
    /** The name of the PDOStatement class to use */
    const PDO_STATEMENT_CLASS = 'Statement';

    /** @var Provider The database provider this connection uses */
    private $provider = null;
    /** @var Server The server we're connecting to */
    private $server = null;
    /** @var string The Data Name Source to connect with */
    private $dsn = '';
    /** @var array The list of driver options to use */
    private $driverOptions = [];
    /** @var bool Whether or not we're connected */
    private $isConnected = false;
    /**
     * The number of transactions we're currently in
     * Useful for nested transactions
     *
     * @var int
     */
    private $transactionCounter = 0;

    /** @var PDO */
    protected $pdo;

    /**
     * @param Provider $provider The database provider this connection uses
     * @param Server $server The server we're connecting to
     * @param string $dsn The Data Name Source to connect with
     * @param array $driverOptions The list of driver options to use
     */
    public function __construct(Provider $provider, Server $server, string $dsn, array $driverOptions = [])
    {
        $this->provider = $provider;
        $this->server = $server;
        $this->dsn = $dsn;
        $this->driverOptions = $driverOptions;
    }

    /**
     * Nested transactions are permitted
     *
     * @inheritdoc
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function beginTransaction()
    {
        $this->connect();

        if (!$this->pdo->transactionCounter++) {
            return $this->pdo->beginTransaction();
        }

        return true;
    }

    /**
     * If we are in a nested transaction and this isn't the final commit of the nested transactions, nothing happens
     *
     * @inheritdoc
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function commit()
    {
        if (!--$this->transactionCounter) {
            return $this->pdo->commit();
        }

        return true;
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorCode()
    {
        $this->connect();

        return $this->pdo->errorCode();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorInfo()
    {
        $this->connect();

        return $this->pdo->errorInfo();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function exec($statement)
    {
        $this->connect();

        return $this->pdo->exec($statement);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function getAttribute($attribute)
    {
        $this->connect();

        return $this->pdo->getAttribute($attribute);
    }

    /**
     * @inheritdoc
     */
    public function getDatabaseProvider()
    {
        return $this->provider;
    }

    /**
     * @inheritdoc
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function inTransaction()
    {
        $this->connect();

        return $this->pdo->inTransaction();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function lastInsertId($sequenceName = null)
    {
        $this->connect();

        return $this->pdo->lastInsertId($sequenceName);
    }

    /**
     * @inheritdoc
     * @return Statement
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function prepare($statement, $driverOptions = [])
    {
        $this->connect();

        return $this->pdo->prepare($statement, $driverOptions);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function query($statement)
    {
        $this->connect();

        return $this->pdo->query($statement);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function quote($string, $parameterType = PDO::PARAM_STR)
    {
        $this->connect();

        return $this->pdo->quote($string, $parameterType);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function rollBack()
    {
        if ($this->transactionCounter >= 0) {
            return $this->pdo->rollBack();
        }

        $this->transactionCounter = 0;

        return true;
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function setAttribute($attribute, $value)
    {
        $this->connect();

        return $this->pdo->setAttribute($attribute, $value);
    }

    /**
     * Attempts to connect to the server, which is done via lazy-connecting
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    private function connect()
    {
        if ($this->isConnected) {
            return;
        }

        $this->pdo = new PDO(
            $this->dsn,
            $this->server->getUsername(),
            $this->server->getPassword(),
            $this->driverOptions
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(
            PDO::ATTR_STATEMENT_CLASS,
            [__NAMESPACE__ . '\\' . self::PDO_STATEMENT_CLASS, [$this]]
        );

        $this->isConnected = true;
    }
}
