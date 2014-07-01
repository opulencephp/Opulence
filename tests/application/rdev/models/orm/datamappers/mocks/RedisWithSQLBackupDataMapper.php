<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the Redis with SQL backup data mapper for use in tests
 */
namespace RDev\Tests\Models\ORM\DataMappers\Mocks;
use RDev\Models;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Exceptions;

class RedisWithSQLBackupDataMapper extends DataMappers\RedisWithSQLBackupDataMapper
{
    public function __construct()
    {
        $this->redisDataMapper = new DataMapper();
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
    public function getRedisDataMapperForTests()
    {
        return $this->redisDataMapper;
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
    protected function getRedisDataMapper(Redis\RDevRedis $redis)
    {
        return new DataMapper();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSQLDataMapper(SQL\ConnectionPool $connectionPool)
    {
        return new DataMapper();
    }

} 