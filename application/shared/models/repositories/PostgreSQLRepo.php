<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a PostgreSQL database as a storage method
 */
namespace RamODev\Application\Shared\Models\Repositories;
use RamODev\Application\Shared\Models;
use RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\Shared\Models\Exceptions;

abstract class PostgreSQLRepo
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
     * Loads an entity from a row of data
     *
     * @param array $row The row of data
     * @return Models\IEntity The entity
     */
    abstract protected function loadEntity(array $row);

    /**
     * Performs the read query for entity(ies) and returns any results
     *
     * @param string $sql The SQL query to run
     * @param array $sqlParameters The list of SQL parameters
     * @param bool $expectSingleResult True if we're expecting a single result, otherwise false
     * @return array|mixed|bool The list of entities or an individual entity if successful, otherwise false
     */
    protected function read($sql, $sqlParameters, $expectSingleResult)
    {
        try
        {
            $results = $this->sqlDatabase->query($sql, $sqlParameters);

            if($expectSingleResult && $results->getNumResults() != 1)
            {
                return false;
            }

            $entities = array();
            $rows = $results->getAllRows();

            foreach($rows as $row)
            {
                $entities[] = $this->loadEntity($row);
            }

            if($expectSingleResult)
            {
                return $entities[0];
            }
            else
            {
                return $entities;
            }
        }
        catch(SQL\Exceptions\SQLException $ex)
        {
            Exceptions\Log::write("Unable to query entities: " . $ex);
        }

        return false;
    }
} 