<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a PostgreSQL database as a storage method
 */
namespace RDev\Models\Repositories;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;

abstract class PostgreSQLRepo
{
    /** @var SQL\RDevPDO The RDevPDO object to use for queries */
    protected $rDevPDO = null;

    /**
     * @param SQL\RDevPDO $rDevPDO The RDevPDO object to use for queries
     */
    public function __construct(SQL\RDevPDO $rDevPDO)
    {
        $this->rDevPDO = $rDevPDO;
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
            $statement = $this->rDevPDO->prepare($sql);
            $statement->bindValues($sqlParameters);
            $statement->execute();

            if($expectSingleResult && $statement->rowCount() != 1)
            {
                return false;
            }

            $entities = [];
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