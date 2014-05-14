<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a relational database connection
 */
namespace RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\Shared\Models\Databases\SQL\Exceptions as SQLExceptions;
use RamODev\Application\Shared\Models\Exceptions;

class SQL extends \PDO
{
    /** @var Server The server we're connecting to */
    private $server = null;
    /** @var bool Whether or not we're connected */
    private $isConnected = false;
    /** @var \PDOStatement|bool The PDO prepared statement that will execute the query */
    private $pdoStatement = false;
    /**
     * The number of transactions we're currently in
     * Useful for nested transactions
     *
     * @var int
     */
    private $transactionCounter = 0;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Starts a transaction
     * Nested transactions are permitted
     *
     * @throws \PDOException Thrown if we're already in a transaction
     */
    public function beginTransaction()
    {
        // There's a chance we haven't connected if we haven't yet queried but have started a transaction
        if(!$this->isConnected)
        {
            $this->connect();
        }

        if(!$this->transactionCounter++)
        {
            parent::beginTransaction();
        }
    }

    /**
     * Commits the transaction
     * If we are in a nested transaction and this isn't the final commit of the nested transactions, nothing happens
     */
    public function commit()
    {
        if(!--$this->transactionCounter)
        {
            parent::commit();
        }
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Executes a query with a prepared statement
     *
     * @param string $query The query to execute
     * @param array $params The parameters to bind to the query
     * @return \PDOStatement The resulting PDO statement
     * @throws SQLExceptions\SQLException Thrown if the query couldn't be executed
     */
    public function query($query, $params = array())
    {
        if(!$this->isConnected)
        {
            $this->isConnected = $this->connect();
        }

        $this->pdoStatement = $this->prepare($query);

        if(count($params) > 0)
        {
            $isSuccessful = $this->pdoStatement->execute($params);
        }
        else
        {
            $isSuccessful = $this->pdoStatement->execute();
        }

        if($isSuccessful === false)
        {
            throw new SQLExceptions\SQLException("Could not run query \"" . $query . "\". PDO error: " . print_r($this->pdoStatement->errorInfo(), true));
        }

        return $this->pdoStatement;
    }

    /**
     * Rolls back a transaction
     *
     * @throws \PDOException Thrown if no transaction is active
     */
    public function rollBack()
    {
        if($this->transactionCounter >= 0)
        {
            parent::rollBack();
        }

        $this->transactionCounter = 0;
    }

    /**
     * Attempts to connect to the server
     *
     * @return bool True if we connected successfully, otherwise false
     */
    private function connect()
    {
        try
        {
            parent::__construct($this->server->getConnectionString(), $this->server->getUsername(), $this->server->getPassword());

            $this->isConnected = true;
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Unable to connect to server \"" . $this->server->getDisplayName() . "\" (" . $this->server->getHost() . "): " . $ex);
            $this->isConnected = false;
        }

        return $this->isConnected;
    }
} 