<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\DataMappers;

use Opulence\Databases\IConnection;
use Opulence\Orm\OrmException;
use PDO;
use PDOException;

/**
 * Defines the base SQL data mapper class
 */
abstract class SqlDataMapper implements IDataMapper
{
    /** Defines a single entity */
    const VALUE_TYPE_ENTITY = 0;
    /** Defines an array of entities */
    const VALUE_TYPE_ARRAY = 1;

    /** @var IConnection The read connection */
    protected $readConnection = null;
    /** @var IConnection The write connection */
    protected $writeConnection = null;

    /**
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     */
    public function __construct(IConnection $readConnection, IConnection $writeConnection)
    {
        $this->readConnection = $readConnection;
        $this->writeConnection = $writeConnection;
    }

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data to load the entity from
     * @return object The entity
     */
    abstract protected function loadEntity(array $hash);

    /**
     * Performs the read query for entity(ies) and returns any results
     *
     * @param string $sql The SQL query to run
     * @param array $sqlParameters The list of SQL parameters
     * @param int $valueType The value type constant designating what kind of data we're expecting to return
     * @param bool $expectSingleResult True if we're expecting a single result, otherwise false
     * @return array|mixed|null The list of entities or an individual entity if successful, otherwise null
     * @throws OrmException Thrown if there was an error querying the entities
     */
    protected function read(string $sql, array $sqlParameters, int $valueType, bool $expectSingleResult = false)
    {
        try {
            $statement = $this->readConnection->prepare($sql);
            $statement->bindValues($sqlParameters);
            $statement->execute();

            if ($expectSingleResult && $statement->rowCount() != 1) {
                throw new OrmException('Failed to find entity');
            }

            $entities = [];
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $entities[] = $this->loadEntity($row);
            }

            if ($valueType == self::VALUE_TYPE_ENTITY) {
                if (count($entities) == 0) {
                    return null;
                }

                return $entities[0];
            } else {
                return $entities;
            }
        } catch (PDOException $ex) {
            throw new OrmException('Unable to query entities', 0, $ex);
        }
    }
}
