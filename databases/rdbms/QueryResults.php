<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Contains result data from a database query
 */
namespace RamODev\Databases\RDBMS;
use RamODev\Databases\RDBMS\Exceptions;

require_once(__DIR__ . "/exceptions/RDBMSException.php");

class QueryResults
{
    /** @var Database The server connection that performed the query */
    private $serverConnection = null;
    /**
     * The results array, which will be left unfilled until we actually try and use the results
     * This will save us a computationally-expensive lookup
     *
     * @var array|null
     */
    private $results = null;

    /**
     * @param Database $serverConnection The server connection that performed the query
     */
    public function __construct(Database $serverConnection)
    {
        $this->serverConnection = $serverConnection;
    }

    /**
     * Gets the number of rows in the results
     *
     * @return int The number of rows in the results
     */
    public function getNumResults()
    {
        return $this->serverConnection->getPreparedStatement()->rowCount();
    }

    /**
     * Gets the result at the specified row/column in the results
     *
     * @param int $row The row number to get data from
     * @param int $col The key/index of the column to get data from
     * @return mixed The result at the input row and column
     * @throws Exceptions\RDBMSException Thrown if there was no result at the specified row/column
     */
    public function getResult($row, $col = 0)
    {
        /**
         * If we haven't actually retrieved the results from the query
         * We only grab the results when we need to use them because it's computationally expensive
         */
        if($this->results == null)
        {
            $this->serverConnection->getPreparedStatement()->setFetchMode(\PDO::FETCH_BOTH);
            $this->results = $this->serverConnection->getPreparedStatement()->fetchAll();
        }

        if(!array_key_exists($row, $this->results) || !array_key_exists($col, $this->results[$row]))
        {
            throw new Exceptions\RDBMSException("No result for row " . $row . " and column \"" . $col . "\"");
        }

        return $this->results[$row][$col];
    }

    /**
     * Gets the results as an associative array
     *
     * @return array The array of results
     */
    public function getRow()
    {
        $this->serverConnection->getPreparedStatement()->setFetchMode(\PDO::FETCH_ASSOC);

        return $this->serverConnection->getPreparedStatement()->fetch();
    }

    /**
     * @return Database
     */
    public function getServerConnection()
    {
        return $this->serverConnection;
    }

    /**
     * Gets whether or not the query yielded any results
     *
     * @return bool True if there are results, otherwise false
     */
    public function hasResults()
    {
        return $this->isSuccessful() && $this->getNumResults() > 0;
    }

    /**
     * Gets whether or not the query was successful
     *
     * @return bool Whether or not the query was successful
     */
    public function isSuccessful()
    {
        return $this->serverConnection->getPreparedStatement() !== false;
    }
} 