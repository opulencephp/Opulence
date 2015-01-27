<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the cached SQL data mapper for use in tests
 */
namespace RDev\Tests\ORM\DataMappers\Mocks;
use RDev\Databases\SQL;
use RDev\ORM\DataMappers;

class CachedSQLDataMapper extends DataMappers\CachedSQLDataMapper
{
    /**
     * @param DataMappers\ISQLDataMapper $sqlDataMapper The SQL data mapper to use
     * @param DataMappers\ICacheDataMapper $cacheDataMapper The cache data mapper to use
     */
    public function __construct(
        DataMappers\ISQLDataMapper $sqlDataMapper = null,
        DataMappers\ICacheDataMapper $cacheDataMapper = null
    )
    {
        if($sqlDataMapper === null)
        {
            $sqlDataMapper = new SQLDataMapper();
        }

        if($cacheDataMapper === null)
        {
            $cacheDataMapper = new CacheDataMapper();
        }

        $this->sqlDataMapper = $sqlDataMapper;
        $this->cacheDataMapper = $cacheDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->read("getAll");
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        return $this->read("getById", [$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadEntity(array $hash)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function setCacheDataMapper($cache)
    {
        $this->cacheDataMapper = new CacheDataMapper();
    }

    /**
     * {@inheritdoc}
     */
    protected function setSQLDataMapper(SQL\ConnectionPool $connectionPool)
    {
        $this->sqlDataMapper = new SQLDataMapper();
    }
} 