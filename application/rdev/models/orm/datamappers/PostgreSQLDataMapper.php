<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that maps domain data to and from PostgreSQL
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;
use RDev\Models\ORM\Repositories\Exceptions as RepoExceptions;

abstract class PostgreSQLDataMapper implements IDataMapper
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
     * {@inheritdoc}
     */
    abstract public function add(Models\IEntity &$entity);

    /**
     * {@inheritdoc}
     */
    abstract public function delete(Models\IEntity &$entity);

    /**
     * {@inheritdoc}
     */
    abstract public function getAll();

    /**
     * {@inheritdoc}
     */
    abstract public function getById($id);

    /**
     * {@inheritdoc}
     */
    abstract public function loadEntity(array $hash);

    /**
     * {@inheritdoc}
     */
    abstract public function update(Models\IEntity &$entity);

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
        catch(\PDOException $ex)
        {
            Exceptions\Log::write("Unable to query entities: " . $ex);
        }

        return false;
    }
} 