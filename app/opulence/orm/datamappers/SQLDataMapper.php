<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines the base SQL data mapper class
 */
namespace Opulence\Orm\DataMappers;

use PDO;
use PDOException;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\IConnection;
use Opulence\Orm\Ids\IdGenerator;
use Opulence\Orm\OrmException;

abstract class SqlDataMapper implements ISqlDataMapper
{
    /** @var ConnectionPool The connection pool to use for queries */
    protected $connectionPool = null;
    /** @var IdGenerator The Id generator this data mapper uses to create new Ids */
    protected $idGenerator = null;

    /**
     * @param ConnectionPool $connectionPool The connection pool to use for queries
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
        $this->setIdGenerator();
    }

    /**
     * @return IdGenerator
     */
    public function getIdGenerator()
    {
        return $this->idGenerator;
    }

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data to load the entity from
     * @param IConnection $connection The connection used to load the entity
     * @return object The entity
     */
    abstract protected function loadEntity(array $hash, IConnection $connection);

    /**
     * Sets the Id generator used by this data mapper
     */
    abstract protected function setIdGenerator();

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
    protected function read($sql, array $sqlParameters, $valueType, $expectSingleResult = false)
    {
        try {
            $connection = $this->connectionPool->getReadConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValues($sqlParameters);
            $statement->execute();

            if ($expectSingleResult && $statement->rowCount() != 1) {
                throw new OrmException("Failed to find entity");
            }

            $entities = [];
            $rows = $statement->fetchAll(PDO::FETCH_BOTH);

            foreach ($rows as $row) {
                $entities[] = $this->loadEntity($row, $connection);
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
            throw new OrmException("Unable to query entities: " . $ex);
        }
    }
} 