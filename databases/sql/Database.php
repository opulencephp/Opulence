<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a relational database
 */
namespace RamODev\Databases\SQL;
use RamODev\Exceptions;
use RamODev\Databases;
use RamODev\Databases\SQL\Exceptions as SQLExceptions;

require_once(__DIR__ . "/exceptions/SQLException.php");
require_once(__DIR__ . "/../Database.php");
require_once(__DIR__ . "/QueryResults.php");

class Database extends Databases\Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \PDO|bool The connection to the database */
    protected $connection = false;
    /** @var \PDOStatement|bool The prepared statement that will execute the query */
    protected $preparedStatement = false;

    /**
     * Closes the connection
     */
    public function close()
    {
        $this->connection = false;
    }

    /**
     * Commits the transaction
     */
    public function commitTransaction()
    {
        $this->connection->commit();
    }

    /**
     * Attempts to connect to the server
     *
     * @return bool True if we connected successfully, otherwise false
     */
    public function connect()
    {
        try
        {
            $this->connection = new \PDO($this->server->getConnectionString(), $this->server->getUsername(), $this->server->getPassword());

            return true;
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Unable to connect to server \"" . $this->server->getDisplayName() . "\" (" . $this->server->getHost() . "): " . $ex);
        }

        return false;
    }

    /**
     * Gets the ID of the last insert performed
     *
     * @param string $idSequenceName The sequence name for the ID column (note: NOT THE COLUMN NAME)
     * @return int The ID of the last insert
     */
    public function getLastInsertID($idSequenceName)
    {
        return $this->connection->lastInsertId($idSequenceName);
    }

    /**
     * @return \PDOStatement The prepared statement that executes the query
     */
    public function getPreparedStatement()
    {
        return $this->preparedStatement;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets whether or not we're connected
     *
     * @return bool Whether or not we're connected
     */
    public function isConnected()
    {
        return $this->connection !== false;
    }

    /**
     * Gets whether or not we're in a transaction
     *
     * @return bool Whether or not we're in a transaction
     */
    public function isInTransaction()
    {
        return $this->connection->inTransaction();
    }

    /**
     * Executes a query with a prepared statement
     *
     * @param string $query The query to execute
     * @param array $params The parameters to bind to the query
     * @return QueryResults A query results object
     * @throws SQLExceptions\SQLException Thrown if the query couldn't be executed
     */
    public function query($query, $params = array())
    {
        if(!$this->isConnected())
        {
            $this->connect();
        }

        $this->preparedStatement = $this->connection->prepare($query);

        if(count($params) > 0)
        {
            $this->preparedStatement->execute($params);
        }
        else
        {
            $this->preparedStatement->execute();
        }

        if($this->preparedStatement === false)
        {
            throw new SQLExceptions\SQLException("Could not run query \"" . $query . "\" with parameters " . var_export($params));
        }

        return new QueryResults($this->preparedStatement);
    }

    /**
     * Rolls back a transaction
     */
    public function rollBackTransaction()
    {
        $this->connection->rollBack();
    }

    /**
     * Starts a transaction
     */
    public function startTransaction()
    {
        // There's a chance we haven't connected if we haven't yet queried but have started a transaction
        if(!$this->isConnected())
        {
            $this->connect();
        }

        $this->connection->beginTransaction();
    }
} 