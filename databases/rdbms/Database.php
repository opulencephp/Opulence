<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Defines a relational database
 */
namespace RamODev\Databases\RDBMS;
use RamODev\Exceptions;
use RamODev\Databases;
use RamODev\Databases\RDBMS\Exceptions as DatabaseExceptions;

require_once(__DIR__ . "/exceptions/DatabaseException.php");
require_once(__DIR__ . "/../Database.php");
require_once(__DIR__ . "/QueryResults.php");

class Database extends Databases\Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \PDO|bool The connection to our database */
    protected $connection = false;
    /** @var \PDOStatement|bool The prepared statement that will execute our query */
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
     * @param array $params The parameters to bind to our query
     * @return QueryResults A query results object
     * @throws DatabaseExceptions\DatabaseException Thrown if the query couldn't be executed
     */
    public function query($query, $params = array())
    {
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
            throw new DatabaseExceptions\DatabaseException("Could not run query \"" . $query . "\" with parameters " . var_export($params));
        }

        return new QueryResults($this);
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
        $this->connection->beginTransaction();
    }
} 