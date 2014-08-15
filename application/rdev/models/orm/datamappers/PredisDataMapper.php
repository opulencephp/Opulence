<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Cache data mapper implemented by the Predis library
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\Databases\NoSQL\Redis;

abstract class PredisDataMapper extends RedisDataMapper
{
    /** @var Redis\RDevPredis The Redis cache to use for queries */
    protected $redis = null;

    /**
     * {@inheritdoc}
     */
    protected function getSetMembersFromRedis($key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValueFromRedis($key)
    {
        return $this->redis->get($key);
    }
} 