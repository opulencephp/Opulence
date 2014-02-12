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
 * Contains result data from a database query
 */
namespace Storage\Databases;
use Storage\Databases\Exceptions;

require_once(__DIR__ . "/exceptions/DatabaseException.php");

class QueryResults
{
    /** @var Connection The server connection that performed the query */
    private $serverConnection = null;
    /**
     * The results array, which will be left unfilled until we actually try and use the results
     * This will save us a computationally-expensive lookup
     *
     * @var array|null
     */
    private $results = null;

    /**
     * @param Connection $serverConnection The server connection that performed the query
     */
    public function __construct(Connection $serverConnection)
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
     * Gets the result at the specified row/column in our results
     *
     * @param int $row The row number to get data from
     * @param int $col The key/index of the column to get data from
     * @returns mixed The result at the input row and column
     * @throws Exceptions\DatabaseException Thrown if there was no result at the specified row/column
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
            throw new Exceptions\DatabaseException("No result for row " . $row . " and column \"" . $col . "\"");
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
     * @return Connection
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