<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a relational database connection
 * The reason the PDO interface is duplicated is because we can then control when the parent PDO object is instantiated
 * This prevents an (expensive) database connection from being made unless we call for it
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
        $this->connect();

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
     * Fetch the SQLSTATE associated with the last operation on the database handle
     *
     * @return string|null The error code if there is one, otherwise null
     */
    public function errorCode()
    {
        $this->connect();

        return parent::errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array An array of error information
     */
    public function errorInfo()
    {
        $this->connect();

        return parent::errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement The statement to execute
     * @return int The number of rows that were modified or deleted by the SQL statement
     */
    public function exec($statement)
    {
        $this->connect();

        return parent::exec($statement);
    }

    /**
     * Retrieve a database connection attribute
     *
     * @param int $attribute The attribute whose value we want
     * @return mixed|null The value if successful, otherwise null
     */
    public function getAttribute($attribute)
    {
        $this->connect();

        return parent::getAttribute($attribute);
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Checks if inside a transaction
     *
     * @return bool True if we're in a transaction, otherwise false
     */
    public function inTransaction()
    {
        $this->connect();

        return parent::inTransaction();
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param string $statement The statement to prepare
     * @param array $driverOptions The driver options to use
     * @return \PDOStatement|bool The statement if successful, otherwise false
     */
    public function prepare($statement, array $driverOptions = array())
    {
        $this->connect();

        return parent::prepare($statement, $driverOptions);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement The statement to prepare and execute
     * @return \PDOStatement The statement if successful, otherwise false
     */
    public function query($statement)
    {
        $this->connect();

        return parent::query($statement);
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string $string The string to be quoted
     * @param int $parameterType Provides a data type hint for drivers that have alternate quoting styles
     * @return string The quoted string if successful, otherwise false
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        $this->connect();

        return parent::quote($string, $parameterType);
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
     * Executes a query with a prepared statement
     *
     * @param string $query The query to execute
     * @param array $params The parameters to bind to the query
     * @return \PDOStatement The resulting PDO statement
     * @throws SQLExceptions\SQLException Thrown if the query couldn't be executed
     */
    /*public function query($query, $params = array())
    {
        $this->connect();

        $statement = $this->prepare($query);

        if(count($params) > 0)
        {
            $isSuccessful = $statement->execute($params);
        }
        else
        {
            $isSuccessful = $statement->execute();
        }

        if($isSuccessful === false)
        {
            throw new SQLExceptions\SQLException("Could not run query \"" . $query . "\". PDO error: " . print_r($statement->errorInfo(), true));
        }

        return $statement;
    }*/

    /**
     * Sets an attribute
     *
     * @param int $attribute The attribute to set
     * @param mixed $value The value to set the attribute to
     * @return bool True if successful, otherwise false
     */
    public function setAttribute($attribute, $value)
    {
        $this->connect();

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Attempts to connect to the server, which is done via lazy-connecting
     *
     * @return bool True if we connected successfully, otherwise false
     */
    private function connect()
    {
        if($this->isConnected)
        {
            return true;
        }

        try
        {
            parent::__construct($this->server->getConnectionString(), $this->server->getUsername(), $this->server->getPassword());

            $this->isConnected = true;
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Unable to connect to server \"" . $this->server->getDisplayName() . "\" (" . $this->server->getHost() . ")");
            $this->isConnected = false;
        }

        return $this->isConnected;
    }
} 