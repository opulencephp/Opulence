<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that uses Redis as a cache with PostgreSQL as a backup
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;

abstract class RedisWithPostgreSQLBackupDataMapper implements IDataMapper
{
    /** @var RedisDataMapper The Redis mapper to use for temporary storage */
    protected $redisDataMapper = null;
    /** @var PostgreSQLDataMapper The SQL database data mapper to use for permanent storage */
    protected $postgreSQLDataMapper = null;

    /**
     * @param Redis\RDevRedis $rDevRedis The RDevRedis object used in the Redis data mapper
     * @param SQL\RDevPDO $rDevPDO The RDevPDO object used in the PostgreSQL data mapper
     */
    public function __construct(Redis\RDevRedis $rDevRedis, SQL\RDevPDO $rDevPDO)
    {
        $this->redisDataMapper = $this->getRedisDataMapper($rDevRedis);
        $this->postgreSQLDataMapper = $this->getPostgreSQLDataMapper($rDevPDO);
    }
} 