<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Databases\IConnection;
use Opulence\Databases\IStatement;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Server;
use PDO;
use PDOException;

/**
 * Defines an extension of the PDO library with lazy-connection
 * In other words, a database connection is only made if we absolutely need to, which gives us a performance gain
 */
class Connection extends PDO implements IConnection
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

        if (!$this->transactionCounter++) {
            parent::beginTransaction();
        }
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
            parent::commit();
        }
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorCode()
    {
        $this->connect();

        return parent::errorCode();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorInfo()
    {
        $this->connect();

        return parent::errorInfo();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function exec($statement)
    {
        $this->connect();

        return parent::exec($statement);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function getAttribute($attribute)
    {
        $this->connect();

        return parent::getAttribute($attribute);
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

        return parent::inTransaction();
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function lastInsertId($sequenceName = null)
    {
        $this->connect();

        return parent::lastInsertId($sequenceName);
    }

    /**
     * @inheritdoc
     * @return Statement
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function prepare($statement, $driverOptions = [])
    {
        $this->connect();

        return parent::prepare($statement, $driverOptions);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function query($statement)
    {
        $this->connect();

        return parent::query($statement);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function quote($string, $parameterType = PDO::PARAM_STR)
    {
        $this->connect();

        return parent::quote($string, $parameterType);
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function rollBack()
    {
        if ($this->transactionCounter >= 0) {
            parent::rollBack();
        }

        $this->transactionCounter = 0;
    }

    /**
     * @inheritdoc
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function setAttribute($attribute, $value)
    {
        $this->connect();

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Attempts to connect to the server, which is done via lazy-connecting
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    private function connect()
    {
        if (!$this->isConnected) {
            parent::__construct(
                $this->dsn,
                $this->server->getUsername(),
                $this->server->getPassword(),
                $this->driverOptions
            );
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            parent::setAttribute(
                PDO::ATTR_STATEMENT_CLASS,
                [__NAMESPACE__ . '\\' . self::PDO_STATEMENT_CLASS, [$this]]
            );

            $this->isConnected = true;
        }
    }
}
