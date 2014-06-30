<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base SQL data mapper class
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;

abstract class SQLDataMapper implements IDataMapper
{
    /** @var SQL\ConnectionPool The connection pool to use for queries */
    protected $connectionPool = null;

    /**
     * @param SQL\ConnectionPool $connectionPool The connection pool to use for queries
     */
    public function __construct(SQL\ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

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
            $statement = $this->connectionPool->getReadConnection()->prepare($sql);
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
        catch(\PDOException $ex)
        {
            Exceptions\Log::write("Unable to query entities: " . $ex);
        }

        return false;
    }
} 