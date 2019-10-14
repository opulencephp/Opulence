<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\DataMappers;

use Exception;
use Opulence\Databases\IConnection;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;
use Opulence\Orm\OrmException;

/**
 * Defines a data mapper that uses cache with SQL as a backup
 */
abstract class CachedSqlDataMapper implements ICachedSqlDataMapper
{
    /** @var ICacheDataMapper The cache mapper to use for temporary storage */
    protected ICacheDataMapper $cacheDataMapper;
    /** @var SqlDataMapper The SQL database data mapper to use for permanent storage */
    protected SqlDataMapper $sqlDataMapper;
    /** @var IIdAccessorRegistry The Id accessor registry (this is null simply for testing) */
    protected ?IIdAccessorRegistry $idAccessorRegistry = null;
    /** @var array The list of actions that are scheduled for committing */
    protected array $scheduledActions = [];

    /**
     * @param mixed $cache The cache object used in the cache data mapper
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry
     */
    public function __construct(
        $cache,
        IConnection $readConnection,
        IConnection $writeConnection,
        IIdAccessorRegistry $idAccessorRegistry
    ) {
        $this->setCacheDataMapper($cache);
        $this->setSqlDataMapper($readConnection, $writeConnection);
        $this->idAccessorRegistry = $idAccessorRegistry;
    }

    /**
     * @inheritdoc
     */
    public function add(object $entity): void
    {
        $this->sqlDataMapper->add($entity);
        $this->scheduleForCacheInsertion($entity);
    }

    /**
     * @inheritdoc
     */
    public function commit(): void
    {
        try {
            foreach ($this->scheduledActions as $action) {
                $entity = $action[1];

                switch ($action[0]) {
                    case 'insert':
                        $this->cacheDataMapper->add($entity);
                        break;
                    case 'update':
                        $this->cacheDataMapper->update($entity);
                        break;
                    case 'delete':
                        $this->cacheDataMapper->delete($entity);
                        break;
                }
            }
        } catch (Exception $ex) {
            throw new OrmException('Commit failed', 0, $ex);
        }

        // Clear our schedules
        $this->scheduledActions = [];
    }

    /**
     * @inheritdoc
     */
    public function delete(object $entity): void
    {
        $this->sqlDataMapper->delete($entity);
        $this->scheduleForCacheDeletion($entity);
    }

    /**
     * @inheritdoc
     */
    public function getById($id): ?object
    {
        return $this->read('getById', [$id]);
    }

    /**
     * @inheritdoc
     */
    public function getCacheDataMapper(): ICacheDataMapper
    {
        return $this->cacheDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function getSqlDataMapper(): SqlDataMapper
    {
        return $this->sqlDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function refreshEntity($id): void
    {
        /**
         * We're refreshing because the entity in cache might have different properties than the one in the SQL database
         * These properties might be used to fully-delete the entity from cache
         * So, we must make sure to use the cache-version of the entity when we delete it from cache
         * Then, we re-fetch it from the SQL database and add it to cache
         */
        $entityFromCache = $this->cacheDataMapper->getById($id);

        if ($entityFromCache !== null) {
            $this->cacheDataMapper->delete($entityFromCache);
        }

        $entityFromSql = $this->sqlDataMapper->getById($id);

        if ($entityFromSql !== null) {
            $this->cacheDataMapper->add($entityFromSql);
        }
    }

    /**
     * @inheritdoc
     */
    public function update(object $entity): void
    {
        $this->sqlDataMapper->update($entity);
        $this->scheduleForCacheUpdate($entity);
    }

    /**
     * Sets the cache data mapper to use in this repo
     *
     * @param mixed $cache The cache object used in the data mapper
     */
    abstract protected function setCacheDataMapper($cache): void;

    /**
     * Sets the SQL data mapper to use in this repo
     *
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     */
    abstract protected function setSqlDataMapper(IConnection $readConnection, IConnection $writeConnection): void;

    /**
     * Attempts to retrieve an entity(ies) from the cache data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our entity retrieval functions
     * @param bool $addDataToCacheOnMiss True if we want to add the entity from the database to cache in case of a cache miss
     * @return object|array|null The entity(ies) if it was found, otherwise null
     * @throws OrmException Thrown if there was any error reading an entity from storage
     */
    protected function read(
        string $funcName,
        array $getFuncArgs = [],
        bool $addDataToCacheOnMiss = true
    ) {
        // Always attempt to retrieve from cache first
        $data = $this->cacheDataMapper->$funcName(...$getFuncArgs);

        /**
         * If an entity wasn't returned or the list of entities was empty, we have no way of knowing if they really
         * don't exist or if they're just not in cache
         * So, we must try looking in the SQL data mapper
         */
        if ($data === null || $data === []) {
            $data = $this->sqlDataMapper->$funcName(...$getFuncArgs);

            // Try to store the data back to cache
            if ($data === null) {
                return null;
            }

            if ($addDataToCacheOnMiss) {
                if (is_array($data)) {
                    foreach ($data as $datum) {
                        $this->cacheDataMapper->add($datum);
                    }
                } else {
                    $this->cacheDataMapper->add($data);
                }
            }
        }

        return $data;
    }

    /**
     * Schedules an entity for deletion from cache
     *
     * @param object $entity The entity to schedule
     */
    protected function scheduleForCacheDeletion(object $entity): void
    {
        $this->scheduledActions[] = ['delete', $entity];
    }

    /**
     * Schedules an entity for insertion into cache
     *
     * @param object $entity The entity to schedule
     */
    protected function scheduleForCacheInsertion(object $entity): void
    {
        $this->scheduledActions[] = ['insert', $entity];
    }

    /**
     * Schedules an entity for update in cache
     *
     * @param object $entity The entity to schedule
     */
    protected function scheduleForCacheUpdate(object $entity): void
    {
        $this->scheduledActions[] = ['update', $entity];
    }
}
