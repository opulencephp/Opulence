<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for database statements to implement
 */
namespace RDev\Models\Databases\SQL;

interface IStatement
{
    /**
     * Binds a parameter to the specified variable name
     *
     * @param mixed $parameter Either the named placeholder, eg ":id", or the 1-indexed position of an unnamed placeholder
     * @param mixed $variable The value of the parameter
     * @param int $dataType The PDO type indicating the type of data we're binding
     * @return bool True if successful, otherwise false
     */
    public function bindParam($parameter, &$variable, $dataType = \PDO::PARAM_STR);

    /**
     * Binds a list of values to the statement
     *
     * @param array $values The mapping of parameter name to a value or to an array
     *      If mapping to an array, the first item should be the value and the second should be the data type constant
     * @return bool True if successful, otherwise false
     */
    public function bindValues(array $values);

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
     * Gets the number of rows affected by the last operation
     *
     * @return int  The number of rows
     */
    public function rowCount();
} 