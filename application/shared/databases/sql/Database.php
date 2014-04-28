<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a relational database
 */
namespace RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\Shared\Exceptions;
use RamODev\Application\Shared\Databases;
use RamODev\Application\Shared\Databases\SQL\Exceptions as SQLExceptions;

class Database extends Databases\Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \PDO|bool The PDO connection to the database */
    protected $pdoConnection = false;
    /** @var \PDOStatement|bool The PDO prepared statement that will execute the query */
    protected $pdoStatement = false;

    /**
     * Closes the connection
     */
    public function close()
    {
        $this->pdoConnection = false;
    }

    /**
     * Commits the transaction
     */
    public function commitTransaction()
    {
        $this->pdoConnection->commit();
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
            $this->pdoConnection = new \PDO($this->server->getConnectionString(), $this->server->getUsername(), $this->server->getPassword());

            return true;
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Unable to connect to server \"" . $this->server->getDisplayName() . "\" (" . $this->server->getHost() . "): " . $ex);
        }

        return false;
    }

    /**
     * Gets the last insert Id
     *
     * @param string $idSequenceName The name of the sequence (not the column) that contains the insert id
     * @return int The Id of the last insert
     */
    public function getLastInsertId($idSequenceName)
    {
        return $this->pdoConnection->lastInsertID($idSequenceName);
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
        return $this->pdoConnection !== false;
    }

    /**
     * Gets whether or not we're in a transaction
     *
     * @return bool Whether or not we're in a transaction
     */
    public function isInTransaction()
    {
        return $this->pdoConnection->inTransaction();
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

        $this->pdoStatement = $this->pdoConnection->prepare($query);

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

        return new QueryResults($this->pdoConnection, $this->pdoStatement);
    }

    /**
     * Rolls back a transaction
     *
     * @throws \PDOException Thrown if no transaction is active
     */
    public function rollBackTransaction()
    {
        $this->pdoConnection->rollBack();
    }

    /**
     * Starts a transaction
     *
     * @throws \PDOException Thrown if we're already in a transaction
     */
    public function startTransaction()
    {
        // There's a chance we haven't connected if we haven't yet queried but have started a transaction
        if(!$this->isConnected())
        {
            $this->connect();
        }

        $this->pdoConnection->beginTransaction();
    }
} 