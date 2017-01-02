<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
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
    protected $cacheDataMapper = null;
    /** @var SqlDataMapper The SQL database data mapper to use for permanent storage */
    protected $sqlDataMapper = null;
    /** @var IIdAccessorRegistry The Id accessor registry */
    protected $idAccessorRegistry = null;
    /** @var object[] The list of entities scheduled for insertion */
    protected $scheduledForCacheInsertion = [];
    /** @var object[] The list of entities scheduled for update */
    protected $scheduledForCacheUpdate = [];
    /** @var object[] The list of entities scheduled for deletion */
    protected $scheduledForCacheDeletion = [];

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
    public function add($entity)
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
            throw new OrmException("Commit failed", 0, $ex);
        }

        // Clear our schedules
        $this->scheduledForCacheInsertion = [];
        $this->scheduledForCacheUpdate = [];
        $this->scheduledForCacheDeletion = [];
    }

    /**
     * @inheritdoc
     */
    public function delete($entity)
    {
        $this->sqlDataMapper->delete($entity);
        $this->scheduleForCacheDeletion($entity);
    }

    /**
     * @inheritdoc
     */
    public function getAll() : array
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
    public function getCacheDataMapper() : ICacheDataMapper
    {
        return $this->cacheDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function getSqlDataMapper() : SqlDataMapper
    {
        return $this->sqlDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function getUnsyncedEntities() : array
    {
        return $this->compareCacheAndSqlEntities(false);
    }

    /**
     * @inheritdoc
     */
    public function refreshCache() : array
    {
        return $this->compareCacheAndSqlEntities(true);
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

        $entityFromSql = $this->sqlDataMapper->getById($id);
        $this->cacheDataMapper->add($entityFromSql);
    }

    /**
     * @inheritdoc
     */
    public function update($entity)
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
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     */
    abstract protected function setSqlDataMapper(IConnection $readConnection, IConnection $writeConnection);

    /**
     * Attempts to retrieve an entity(ies) from the cache data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our entity retrieval functions
     * @param bool $addDataToCacheOnMiss True if we want to add the entity from the database to cache in case of a cache miss
     * @param array $setFuncArgs The array of function arguments to pass into the set functions in the case of a cache miss
     * @return object|array|null The entity(ies) if it was found, otherwise null
     */
    protected function read(
        string $funcName,
        array $getFuncArgs = [],
        bool $addDataToCacheOnMiss = true,
        array $setFuncArgs = []
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
                        $this->cacheDataMapper->add($datum, ...$setFuncArgs);
                    }
                } else {
                    $this->cacheDataMapper->add($data, ...$setFuncArgs);
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
    protected function scheduleForCacheDeletion($entity)
    {
        $this->scheduledForCacheDeletion[] = $entity;
    }

    /**
     * Schedules an entity for insertion into cache
     *
     * @param object $entity The entity to schedule
     */
    protected function scheduleForCacheInsertion($entity)
    {
        $this->scheduledForCacheInsertion[] = $entity;
    }

    /**
     * Schedules an entity for update in cache
     *
     * @param object $entity The entity to schedule
     */
    protected function scheduleForCacheUpdate($entity)
    {
        $this->scheduledForCacheUpdate[] = $entity;
    }

    /**
     * Does the comparison of entities in cache to entities in the SQL database
     * Also performs refresh if the user chooses to do so
     *
     * @param bool $doRefresh Whether or not to refresh any unsynced entities
     * @return object[] The list of entities that were not already synced
     *      The "missing" list contains the entities that were not in cache
     *      The "differing" list contains the entities in cache that were not the same as SQL
     *      The "additional" list contains entities in cache that were not at all in SQL
     * @throws OrmException Thrown if there was an error getting the unsynced entities
     */
    private function compareCacheAndSqlEntities(bool $doRefresh) : array
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
            if (isset($cacheEntities[$sqlId])) {
                // The entity appears in cache
                $cacheEntity = $cacheEntities[$sqlId];

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
     * @param object[] $entities The list of entities
     * @return object[] The keyed array
     */
    private function keyEntityArray(array $entities) : array
    {
        $keyedArray = [];

        foreach ($entities as $entity) {
            $keyedArray[$this->idAccessorRegistry->getEntityId($entity)] = $entity;
        }

        return $keyedArray;
    }
} 