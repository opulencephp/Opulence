<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the cached SQL data mapper for use in tests
 */
namespace RDev\Tests\Models\ORM\DataMappers\Mocks;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Exceptions;

class CachedSQLDataMapper extends DataMappers\CachedSQLDataMapper
{
    public function __construct()
    {
        $this->cacheDataMapper = new CacheDataMapper();
        $this->sqlDataMapper = new DataMapper();
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
     * @return DataMapper
     */
    public function getCacheDataMapperForTests()
    {
        return $this->cacheDataMapper;
    }

    /**
     * @return DataMapper
     */
    public function getSQLDataMapperForTests()
    {
        return $this->sqlDataMapper;
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
    protected function getCacheDataMapper($cache)
    {
        return new CacheDataMapper();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSQLDataMapper(SQL\ConnectionPool $connectionPool)
    {
        return new DataMapper();
    }
} 