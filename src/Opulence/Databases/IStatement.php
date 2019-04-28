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

use PDO;

/**
 * Defines the interface for database statements to implement
 */
interface IStatement
{
    /**
     * Binds a parameter to the specified variable name
     *
     * @param mixed $parameter Either the named placeholder, eg ":id", or the 1-indexed position of an unnamed placeholder
     * @param mixed $variable The value of the parameter
     * @param int|null $dataType The PDO type indicating the type of data we're binding
     * @param int|null $length Length of the data type
     * @return bool True if successful, otherwise false
     */
    public function bindParam($parameter, &$variable, $dataType = PDO::PARAM_STR, $length = null);

    /**
     * Binds a value to the statement
     *
     * @param mixed $parameter Either the named placeholder, eg ":id", or the 1-indexed position of an unnamed placeholder
     * @param mixed $value The value of the parameter
     * @param int $dataType The PDO type indicating the type of data we're binding
     * @return bool True if successful, otherwise false
     */
    public function bindValue($parameter, $value, $dataType = PDO::PARAM_STR);

    /**
     * Binds a list of values to the statement
     *
     * @param array $values The mapping of parameter name to a value or to an array
     *      If mapping to an array, the first item should be the value and the second should be the data type constant
     * @return bool True if successful, otherwise false
     */
    public function bindValues(array $values): bool;

    /**
     * Frees up the connection to the server, but lets the statement be executed again
     * Useful for database drivers that do not support executing another statement when a previously-executed statement
     * still has unfetched rows
     *
     * @return bool True if successful, otherwise false
     */
    public function closeCursor();

    /**
     * Gets the number of columns in the result set
     *
     * @return int The number of columns in the result set
     */
    public function columnCount();

    /**
     * Gets the SQLSTATE associated with the last operation
     *
     * @return string The error code
     */
    public function errorCode();

    /**
     * Gets information about the error that occurred in the last operation
     *
     * @return array The error info
     */
    public function errorInfo();

    /**
     * Executes a prepared statement
     *
     * @param array|null $parameters The list of parameters to bind to the statement
     * @return bool True if successful, otherwise false
     */
    public function execute($parameters = null);

    /**
     * Fetches the next row from a result set
     *
     * @param int $fetchStyle The PDO::FETCH_* constant that specifies how the next row will be returned
     * @return array|bool The row if successful, otherwise false
     */
    public function fetch($fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE);

    /**
     * Fetches all the result rows
     *
     * @param int $fetchStyle The PDO::FETCH_* constant that specifies how the next row will be returned
     * @return array The list of result rows if successful, otherwise false
     */
    public function fetchAll($fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE);

    /**
     * Gets a single column from the next row of a result set
     *
     * @param int $columnNumber The 0-indexed number of the column you wish to retrieve from the row
     * @return mixed The data from the specified column
     */
    public function fetchColumn($columnNumber = 0);

    /**
     * Gets the number of rows affected by the last operation
     *
     * @return int  The number of rows
     */
    public function rowCount();

    /**
     * Sets the fetch mode to be used for all fetch* methods
     *
     * @param int $fetchMode The PDO::FETCH_* constant that specifies how the next row will be returned
     * @return bool True if successful, otherwise false
     */
    public function setFetchMode($fetchMode);
}
