<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the cached SQL data mapper for use in tests
 */
namespace Opulence\Tests\ORM\DataMappers\Mocks;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\ORM\Ids\IIdAccessorRegistry;
use Opulence\ORM\DataMappers\CachedSQLDataMapper as BaseCachedSQLDataMapper;
use Opulence\ORM\DataMappers\ICacheDataMapper;
use Opulence\ORM\DataMappers\ISQLDataMapper;

class CachedSQLDataMapper extends BaseCachedSQLDataMapper
{
    /**
     * @param ISQLDataMapper $sqlDataMapper The SQL data mapper to use
     * @param ICacheDataMapper $cacheDataMapper The cache data mapper to use
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry to use
     */
    public function __construct(
        ISQLDataMapper $sqlDataMapper = null,
        ICacheDataMapper $cacheDataMapper = null,
        IIdAccessorRegistry $idAccessorRegistry = null
    ) {
        if ($sqlDataMapper === null) {
            $sqlDataMapper = new SQLDataMapper();
        }

        if ($cacheDataMapper === null) {
            $cacheDataMapper = new CacheDataMapper();
        }

        $this->sqlDataMapper = $sqlDataMapper;
        $this->cacheDataMapper = $cacheDataMapper;
        $this->idAccessorRegistry = $idAccessorRegistry;
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
    public function loadEntity(array $hash)
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    protected function setCacheDataMapper($cache)
    {
        $this->cacheDataMapper = new CacheDataMapper();
    }

    /**
     * @inheritdoc
     */
    protected function setSQLDataMapper(ConnectionPool $connectionPool)
    {
        $this->sqlDataMapper = new SQLDataMapper();
    }
} 