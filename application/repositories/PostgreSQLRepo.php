<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a PostgreSQL database as a storage method
 */
namespace RamODev\Repositories;
use RamODev\Databases\SQL;
use RamODev\Exceptions;

class PostgreSQLRepo
{
    /** @var SQL\Database The relational database to use for queries */
    protected $sqlDatabase = null;

    /**
     * @param SQL\Database $sqlDatabase The database to use for queries
     */
    public function __construct(SQL\Database $sqlDatabase)
    {
        $this->sqlDatabase = $sqlDatabase;
    }

    /**
     * Performs the read query for object(s) and returns any results
     *
     * @param string $sql The SQL query to run
     * @param array $sqlParameters The list of SQL parameters
     * @param string $objectsFromRowsFuncName The name of the (protected or public) method to run, which returns an array of objects from rows of data
     * @param bool $expectSingleResult True if we're expecting a single result, otherwise false and we're expecting an array of results
     * @return array|mixed|bool The list of objects or an individual object if successful, otherwise false
     * @throws Exceptions\InvalidInputException Thrown if we're expecting a single result, but we didn't get one
     */
    protected function read($sql, $sqlParameters, $objectsFromRowsFuncName, $expectSingleResult)
    {
        try
        {
            $results = $this->sqlDatabase->query($sql, $sqlParameters);

            if($expectSingleResult && $results->getNumResults() != 1)
            {
                throw new Exceptions\InvalidInputException("Couldn't find a single object with input query");
            }

            $objects = call_user_func_array(array($this, $objectsFromRowsFuncName), array($results->getAllRows()));

            if($expectSingleResult)
            {
                return $objects[0];
            }
            else
            {
                return $objects;
            }
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Exceptions\Log::write("Unable to query objects: " . $ex);
        }

        return false;
    }
} 