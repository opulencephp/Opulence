<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a data mapper that uses cache with SQL as a backup
 */
namespace Opulence\ORM\DataMappers;

use Exception;
use Opulence\Databases\ConnectionPool;
use Opulence\ORM\IEntity;
use Opulence\ORM\ORMException;

abstract class CachedSQLDataMapper implements ICachedSQLDataMapper
{
    /** @var ICacheDataMapper The cache mapper to use for temporary storage */
    protected $cacheDataMapper = null;
    /** @var SQLDataMapper The SQL database data mapper to use for permanent storage */
    protected $sqlDataMapper = null;
    /** @var IEntity[] The list of entities scheduled for insertion */
    protected $scheduledForCacheInsertion = [];
    /** @var IEntity[] The list of entities scheduled for update */
    protected $scheduledForCacheUpdate = [];
    /** @var IEntity[] The list of entities scheduled for deletion */
    protected $scheduledForCacheDeletion = [];

    /**
     * @param mixed $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct($cache, ConnectionPool $connectionPool)
    {
        $this->setCacheDataMapper($cache);
        $this->setSQLDataMapper($connectionPool);
    }

    /**
     * @inheritdoc
     */
    public function add(IEntity &$entity)
    {
        $this->sqlDataMapper->add($entity);
        $this->scheduleForCacheInsertion($entity);
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        try {
            // Insert entities
            foreach ($this->scheduledForCacheInsertion as $entity) {
                $this->cacheDataMapper->add($entity);
            }

            // Update entities
            foreach ($this->scheduledForCacheUpdate as $entity) {
                $this->cacheDataMapper->update($entity);
            }

            // Delete entities
            foreach ($this->scheduledForCacheDeletion as $entity) {
                $this->cacheDataMapper->delete($entity);
            }
        } catch (Exception $ex) {
            throw new ORMException($ex->getMessage());
        }

        // Clear our schedules
        $this->scheduledForCacheInsertion = [];
        $this->scheduledForCacheUpdate = [];
        $this->scheduledForCacheDeletion = [];
    }

    /**
     * @inheritdoc
     */
    public function delete(IEntity &$entity)
    {
        $this->sqlDataMapper->delete($entity);
        $this->scheduleForCacheDeletion($entity);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->read("getAll");
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        return $this->read("getById", [$id]);
    }

    /**
     * @inheritdoc
     */
    public function getCacheDataMapper()
    {
        return $this->cacheDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function getIdGenerator()
    {
        return $this->sqlDataMapper->getIdGenerator();
    }

    /**
     * @inheritdoc
     */
    public function getSqlDataMapper()
    {
        return $this->sqlDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function getUnsyncedEntities()
    {
        return $this->compareCacheAndSQLEntities(false);
    }

    /**
     * @inheritdoc
     */
    public function refreshCache()
    {
        return $this->compareCacheAndSQLEntities(true);
    }

    /**
     * @inheritdoc
     */
    public function refreshEntity($id)
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

        $entityFromSQL = $this->sqlDataMapper->getById($id);
        $this->cacheDataMapper->add($entityFromSQL);
    }

    /**
     * @inheritdoc
     */
    public function update(IEntity &$entity)
    {
        $this->sqlDataMapper->update($entity);
        $this->scheduleForCacheUpdate($entity);
    }

    /**
     * Sets the cache data mapper to use in this repo
     *
     * @param mixed $cache The cache object used in the data mapper
     */
    abstract protected function setCacheDataMapper($cache);

    /**
     * Sets the SQL data mapper to use in this repo
     *
     * @param ConnectionPool $connectionPool The connection pool used in the data mapper
     */
    abstract protected function setSQLDataMapper(ConnectionPool $connectionPool);

    /**
     * Attempts to retrieve an entity(ies) from the cache data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our entity retrieval functions
     * @param bool $addDataToCacheOnMiss True if we want to add the entity from the database to cache in case of a cache miss
     * @param array $setFuncArgs The array of function arguments to pass into the set functions in the case of a cache miss
     * @return IEntity|array|null The entity(ies) if it was found, otherwise null
     */
    protected function read($funcName, array $getFuncArgs = [], $addDataToCacheOnMiss = true, array $setFuncArgs = [])
    {
        // Always attempt to retrieve from cache first
        $data = call_user_func_array([$this->cacheDataMapper, $funcName], $getFuncArgs);

        /**
         * If an entity wasn't returned or the list of entities was empty, we have no way of knowing if they really
         * don't exist or if they're just not in cache
         * So, we must try looking in the SQL data mapper
         */
        if ($data === null || $data === []) {
            $data = call_user_func_array([$this->sqlDataMapper, $funcName], $getFuncArgs);

            // Try to store the data back to cache
            if ($data === null) {
                return null;
            }

            if ($addDataToCacheOnMiss) {
                if (is_array($data)) {
                    foreach ($data as $datum) {
                        call_user_func_array([$this->cacheDataMapper, "add"], array_merge([&$datum], $setFuncArgs));
                    }
                } else {
                    call_user_func_array([$this->cacheDataMapper, "add"], array_merge([&$data], $setFuncArgs));
                }
            }
        }

        return $data;
    }

    /**
     * Schedules an entity for deletion from cache
     *
     * @param IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheDeletion(IEntity $entity)
    {
        $this->scheduledForCacheDeletion[] = $entity;
    }

    /**
     * Schedules an entity for insertion into cache
     *
     * @param IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheInsertion(IEntity $entity)
    {
        $this->scheduledForCacheInsertion[] = $entity;
    }

    /**
     * Schedules an entity for update in cache
     *
     * @param IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheUpdate(IEntity $entity)
    {
        $this->scheduledForCacheUpdate[] = $entity;
    }

    /**
     * Does the comparison of entities in cache to entities in the SQL database
     * Also performs refresh if the user chooses to do so
     *
     * @param bool $doRefresh Whether or not to refresh any unsynced entities
     * @return IEntity[] The list of entities that were not already synced
     *      The "missing" list contains the entities that were not in cache
     *      The "differing" list contains the entities in cache that were not the same as SQL
     *      The "additional" list contains entities in cache that were not at all in SQL
     * @throws ORMException Thrown if there was an error getting the unsynced entities
     */
    private function compareCacheAndSQLEntities($doRefresh)
    {
        // If there was an issue grabbing all entities in cache, null will be returned
        $unkeyedCacheEntities = $this->cacheDataMapper->getAll();

        if ($unkeyedCacheEntities === null) {
            $unkeyedCacheEntities = [];
        }

        $cacheEntities = $this->keyEntityArray($unkeyedCacheEntities);
        $sqlEntities = $this->keyEntityArray($this->sqlDataMapper->getAll());
        $unsyncedEntities = [
            "missing" => [],
            "differing" => [],
            "additional" => []
        ];

        // Compare the entities in the SQL database to those in cache
        foreach ($sqlEntities as $sqlId => $sqlEntity) {
            if (isset($cacheEntities[$sqlEntity->getId()])) {
                // The entity appears in cache
                $cacheEntity = $cacheEntities[$sqlEntity->getId()];

                if ($sqlEntity != $cacheEntity) {
                    $unsyncedEntities["differing"][] = $sqlEntity;

                    if ($doRefresh) {
                        // Sync the entity in cache with the one in SQL
                        $this->cacheDataMapper->delete($cacheEntity);
                        $this->cacheDataMapper->add($sqlEntity);
                    }
                }
            } else {
                // The entity was not in cache
                $unsyncedEntities["missing"][] = $sqlEntity;

                if ($doRefresh) {
                    // Add the entity to cache
                    $this->cacheDataMapper->add($sqlEntity);
                }
            }
        }

        // Find entities that only appear in cache
        $cacheOnlyIds = array_diff(array_keys($cacheEntities), array_keys($sqlEntities));

        foreach ($cacheOnlyIds as $entityId) {
            $cacheEntity = $cacheEntities[$entityId];
            $unsyncedEntities["additional"][] = $cacheEntity;

            if ($doRefresh) {
                // Remove the entity that only appears in cache
                $this->cacheDataMapper->delete($cacheEntity);
            }
        }

        return $unsyncedEntities;
    }

    /**
     * Converts a list of entities to a keyed array of those entities
     * The keys are the entity Ids
     *
     * @param IEntity[] $entities The list of entities
     * @return IEntity[] The keyed array
     */
    private function keyEntityArray(array $entities)
    {
        $keyedArray = [];

        foreach ($entities as $entity) {
            $keyedArray[$entity->getId()] = $entity;
        }

        return $keyedArray;
    }
} 