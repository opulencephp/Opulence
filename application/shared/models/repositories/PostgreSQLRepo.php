<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a PostgreSQL database as a storage method
 */
namespace RDev\Application\Shared\Models\Repositories;
use RDev\Application\Shared\Models;
use RDev\Application\Shared\Models\Databases\SQL;
use RDev\Application\Shared\Models\Exceptions;

abstract class PostgreSQLRepo
{
    /** @var SQL\SQL The SQL object to use for queries */
    protected $sql = null;

    /**
     * @param SQL\SQL $sql The SQL object to use for queries
     */
    public function __construct(SQL\SQL $sql)
    {
        $this->sql = $sql;
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
    protected function read($sql, array $sqlParameters, $expectSingleResult)
    {
        try
        {
            $statement = $this->sql->prepare($sql);
            $statement->execute($sqlParameters);

            if($expectSingleResult && $statement->rowCount() != 1)
            {
                return false;
            }

            $entities = array();
            $rows = $statement->fetchAll(\PDO::FETCH_BOTH);

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