<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases;

use Opulence\Databases\Providers\Provider;
use PDO;
use PDOException;

/**
 * Defines the database connection interface, which must be implemented by any classes that wish to be used
 * as a database connection in this application
 */
interface IConnection
{
    /**
     * Begins a transaction
     * Nested transactions are permitted
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function beginTransaction();

    /**
     * Commits a transaction
     * If we are in a nested transaction and this isn't the final commit of the nested transactions, nothing happens
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function commit();

    /**
     * Gets the SQLSTATE of the last query, if there was one
     *
     * @return string|null The error code, if one was set, otherwise false
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorCode();

    /**
     * Gets information about the last query error
     *
     * @return array The array of information about the last operation
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function errorInfo();

    /**
     * Executes an SQL statement in a single call
     *
     * @param string $statement The SQL statement to execute
     * @return int The number of rows affected by the statement
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function exec($statement);

    /**
     * Gets the database provider used by this connection
     *
     * @return Provider The database provider used by this connection
     */
    public function getDatabaseProvider();

    /**
     * Gets the server used in the connection
     *
     * @return Server The server used in the connection
     */
    public function getServer();

    /**
     * Gets whether or not we're in a transaction
     *
     * @return bool True if we're in a transaction, otherwise false
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function inTransaction();

    /**
     * Gets the last insert Id
     *
     * @param string $sequenceName The name of the sequence whose Id we want
     * @return string The Id of the last row in the input sequence
     */
    public function lastInsertId($sequenceName = null);

    /**
     * Prepares an SQL statement for execution
     *
     * @param string $statement The SQL statement to execute
     * @return IStatement The statement
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function prepare($statement);

    /**
     * Executes an SQL statement and gets the statement object
     *
     * @param string $statement The SQL statement to execute
     * @return IStatement The statement
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function query($statement);

    /**
     * Quotes a string for use in a query
     *
     * @param string $string The string to quote
     * @param int $parameterType The PDO constant that indicates the type of the parameter
     * @return string The quoted string
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function quote($string, $parameterType = PDO::PARAM_STR);

    /**
     * Rolls back the transaction
     *
     * @throws PDOException Thrown if there was an error connecting to the database
     */
    public function rollBack();
}
